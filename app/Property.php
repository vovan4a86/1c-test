<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Zenwalker\CommerceML\Model\Property as MlProperty;

class Property extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

}
