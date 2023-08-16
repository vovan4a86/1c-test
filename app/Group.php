<?php

namespace App;

use Bigperson\Exchange1C\Interfaces\GroupInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Group extends Model implements GroupInterface
{
    protected $guarded = ['id'];

    public $timestamps = false;
    /**
     * Создание дерева групп
     * в параметр передаётся массив всех групп (import.xml > Классификатор > Группы)
     * $groups[0]->parent - родительская группа
     * $groups[0]->children - дочерние группы
     *
     * @param \Zenwalker\CommerceML\Model\Group[] $groups
     * @return void
     */
    public static function createTree1c($groups)
    {
        Log::info('Start createTree');
        foreach ($groups as $group) {
            self::createByML($group);
            if ($children = $group->getChildren()) {
                self::createTree1c($children);
            }
        }
    }

    /**
     * Создаём группу по модели группы CommerceML
     * проверяем все дерево родителей группы, если родителя нет в базе - создаём
     *
     * @param \Zenwalker\CommerceML\Model\Group $group
     * @return Group|array|null
     */
    public static function createByML(Group $group)
    {
        /**
         * @var Group $parent
         */
        if (!$model = Group::query()->where('accounting_id', $group->id)) {
            $model = new self;
            $model->accounting_id = $group->id;
        }
        $model->name = $group->name;
        if ($parent = $group->getParent()) {
            $parentModel = self::createByML($parent);
            $model->parent_id = $parentModel->id;
            unset($parentModel);
        } else {
            $model->parent_id = null;
        }
        $model->save();
        return $model;
    }

    /**
     * Возвращаем имя поля в базе данных, в котором хранится ID из 1с
     *
     * @return string
     */
    public static function getIdFieldName1c(): string
    {
        return 'accounting_id';
    }

    public function getPrimaryKey(): string
    {
        return 'id';
    }
}
