<?php

namespace App;

use Bigperson\Exchange1C\Interfaces\GroupInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Category extends Model implements GroupInterface
{
    protected $guarded = ['id'];

    /**
     * Создание дерева групп
     * в параметр передаётся массив всех групп (import.xml > Классификатор > Группы)
     * $groups[0]->parent - родительская группа
     * $groups[0]->children - дочерние группы.
     *
     * @param \Zenwalker\CommerceML\Model\Group[] $groups
     *
     * @return void
     */
    public static function createTree1c($groups, $parent = 0)
    {
        foreach ($groups as $group) {
            $category = Category::query()->where('name', $group->name)->first();

            if (!$category) {
                $category = Category::query()->create(
                    [
                        'name' => $group->name,
                        'parent_id' => $parent,
                        'accounting_id' => $group->id
                    ]
                );
            }

            if ($children = $group->children) {
                self::createTree1c($children, $category->id);
            }
        }
    }

    /**
     * Возвращаем имя поля в базе данных, в котором хранится ID из 1с
     *
     * @return string
     */
    public static function getIdFieldName1c()
    {
        return 'accounting_id';
    }

    /**
     * Возвращаем id сущности.
     *
     * @return int|string
     */
    public function getPrimaryKey()
    {
        return 'id';
    }

}
