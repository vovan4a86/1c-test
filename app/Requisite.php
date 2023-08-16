<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Requisite extends Model
{

    protected $guarded = ['id'];
    public $timestamps = false;

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('value');
    }

}
