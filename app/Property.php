<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Zenwalker\CommerceML\Model\Property as MlProperty;

/**
 * @method static createByMl(MlProperty $property)
 */
class Property extends MlProperty
{
    protected $guarded = ['id'];
    public $timestamps = false;

}
