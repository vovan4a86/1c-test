<?php

namespace App;

use Bigperson\Exchange1C\Interfaces\GroupInterface;
use Bigperson\Exchange1C\Interfaces\OfferInterface;
use Bigperson\Exchange1C\Interfaces\ProductInterface;
use Zenwalker\CommerceML\Model\Property;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements ProductInterface
{
    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function requisites()
    {
        return $this->belongsToMany(Requisite::class)->withPivot('value');
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class)->withPivot('value');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    //1c now

    /**
     * Возвращаем имя поля в базе данных, в котором хранится ID из 1с
     *
     * @return string
     */
    public static function getIdFieldName1c(): string
    {
        return '1c_product_id';
    }

    /**
     * Получение уникального идентификатора продукта в рамках БД сайта.
     *
     * @return int|string
     */
    public function getPrimaryKey(): string
    {
        return 'id';
    }

    /**
     * Если по каким то причинам файлы import.xml или offers.xml были модифицированы и какие то данные
     * не попадают в парсер, в самом конце вызывается данный метод, в $product и $cml можно получить все
     * возможные данные для ручного парсинга.
     *
     * @param \Zenwalker\CommerceML\CommerceML    $cml
     * @param \Zenwalker\CommerceML\Model\Product $product
     *
     * @return void
     */
    public function setRaw1cData($cml, $product)
    {
        // TODO: Implement setRaw1cData() method.
    }

    /**
     * Установка реквизитов, (import.xml > Каталог > Товары > Товар > ЗначенияРеквизитов > ЗначениеРеквизита)
     * $name - Наименование
     * $value - Значение.
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public function setRequisite1c($name, $value)
    {
        if (!$requisite = Requisite::query()->where('name',$name)->first()) {
            $requisite = new Requisite();
            $requisite->name = $name;
            $requisite->save();
        }

        $this->requisites()->updateExistingPivot($requisite->id, ['value' => $value]);
    }

    /**
     * Предпологается, что дерево групп у Вас уже создано (\Bigperson\Exchange1C\Interfaces\GroupInterface::createTree1c).
     *
     * @param \Zenwalker\CommerceML\Model\Group $group
     *
     * @return mixed
     */
    public function setGroup1c($group)
    {
        $id = Group::query()->where('accounting_id', $group->id)->get(['id']);
        $this->group_id = $id;
    }

    /**
     * import.xml > Классификатор > Свойства > Свойство
     * $property - Свойство товара.
     *
     * import.xml > Классификатор > Свойства > Свойство > Значение
     * $property->value - Разыменованное значение (string)
     *
     * import.xml > Классификатор > Свойства > Свойство > ВариантыЗначений > Справочник
     * $property->getValueModel() - Данные по значению, Ид значения, и т.д
     *
     * @param \Zenwalker\CommerceML\Model\Property $property
     *
     * @return void
     */
    public function setProperty1c($property)
    {
        $propertyModel = Property::query()->where('accounting_id', $property->id)->first();
        $propertyValue = $property->getValueModel();
        if ($propertyAccountingId = (string)$propertyValue->ИдЗначения) {
            $value = PropertyValue::query()->where('accounting_id', $propertyAccountingId);
            $attributes = ['property_value_id' => $value->id];
        } else {
            $attributes = ['value' => $propertyValue->value];
        }

        $this->properties()->updateExistingPivot($propertyModel->id, $attributes);
    }

    /**
     * Создание всех свойств продутка
     * import.xml > Классификатор > Свойства.
     *
     * $properties[]->availableValues - список доступных значений, для этого свойства
     * import.xml > Классификатор > Свойства > Свойство > ВариантыЗначений > Справочник
     *
     * @param Property $properties
     *
     * @return mixed
     */
    public static function createProperties1c($properties)
    {
        /**
         * @var \Zenwalker\CommerceML\Model\Property $property
         */
        foreach ($properties as $property) {
            $propertyModel = Property::createByMl($property);
            foreach ($property->getAvailableValues() as $value) {
                if (!$propertyValue = PropertyValue::query()->where('accounting_id', $value->id)) {
                    $propertyValue = new PropertyValue();
                    $propertyValue->name = (string)$value->Значение;
                    $propertyValue->property_id = $propertyModel->id;
                    $propertyValue->accounting_id = (string)$value->ИдЗначения;
                    $propertyValue->save();
                    unset($propertyValue);
                }
            }
        }
    }

    /**
     * @param string $path
     * @param string $caption
     *
     * @return void
     */
    public function addImage1c($path, $caption)
    {
//        if (!$this->getImages()->andWhere(['md5' => md5_file($path)])->exists()) {
//            $this->addPivot(FileUpload::startUpload($path)->process(), ProductImage::class, ['caption' => $caption]);
//        }

        if (!$this->image) {
            $this->image = $path;
            $this->article = $caption; //название пока в article кинул
        }
    }

    /**
     * @return GroupInterface
     */
    public function getGroup1c(): GroupInterface
    {
        return $this->group;
    }

    /**
     * @param \Zenwalker\CommerceML\Model\Offer $offer
     *
     * @return OfferInterface
     */
    public function getOffer1c($offer)
    {
        // TODO: Implement getOffer1c() method.
    }

    /**
     * @param \Zenwalker\CommerceML\Model\Product $product
     *
     * @return self
     */
    public static function createModel1c(\Zenwalker\CommerceML\Model\Product $product)
    {
        // TODO: Implement createModel1c() method.
    }

    /**
     * @param string $id
     *
     * @return ProductInterface|null
     */
    public static function findProductBy1c(string $id): ProductInterface
    {
        // TODO: Implement findProductBy1c() method.
    }
}
