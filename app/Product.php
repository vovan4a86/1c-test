<?php

namespace App;

use Bigperson\Exchange1C\Interfaces\GroupInterface;
use Bigperson\Exchange1C\Interfaces\ProductInterface;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    //1c now

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

    public function getPrimaryKey(): string
    {
        return 'id';
    }

    public static function getIdFieldName1c()
    {
        // TODO: Implement getIdFieldName1c() method.
    }

    public function setRaw1cData($cml, $product)
    {
        // TODO: Implement setRaw1cData() method.
    }

    public function setRequisite1c($name, $value)
    {
        if (!$requisite = Requisite::find($name)) {
            $requisite = new Requisite();
            $requisite->name = $name;
            $requisite->save();
        }

        $this->requisites()->updateExistingPivot($requisite->id, ['value' => $value]);
    }

    public function setGroup1c($group)
    {
        $id = Group::query()->where('accounting_id', $group->id)->get(['id']);
        $this->group_id = $id;
    }

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
     * $property - Свойство товара (import.xml > Классификатор > Свойства > Свойство)
     * $property->value - Разыменованное значение (string) (import.xml > Классификатор > Свойства > Свойство > Значение)
     * $property->getValueModel() - Данные по значению, Ид значения, и т.д (import.xml > Классификатор > Свойства > Свойство > ВариантыЗначений > Справочник)
     *
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

    public function getOffer1c($offer)
    {
        // TODO: Implement getOffer1c() method.
    }

    public static function createModel1c($product)
    {
        // TODO: Implement createModel1c() method.
    }

    public static function findProductBy1c(string $id): ProductInterface
    {
        // TODO: Implement findProductBy1c() method.
    }
}
