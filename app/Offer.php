<?php

namespace App;

use Bigperson\Exchange1C\Interfaces\GroupInterface;
use Bigperson\Exchange1C\Interfaces\OfferInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed accounting_id
 */
class Offer extends Model implements OfferInterface
{
    protected $guarded = ['id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return GroupInterface
     */
    public function getGroup1c(): GroupInterface
    {
        return $this->product()->group;
    }

    public function setPrice1c($price)
    {
        // TODO: Implement setPrice1c() method.
    }

    /**
     * @param $types
     * @return \App\Offer|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function createPriceTypes1c($types)
    {
        foreach ($types as $type) {
            if (!$priceType = PriceType::query()->where('accounting_id', $type->id)->first()) {
                $priceType = new self;
                $priceType->accounting_id = $type->id;
            }
            $priceType->name = $type->name;
            $priceType->currency = (string)$type->Валюта;
            $priceType->save();
//            if ($priceType->getDirtyAttributes()) {
//                $priceType->save();
//            }
            return $priceType;
        }
    }

    public function setSpecification1c($specification)
    {
        // TODO: Implement setSpecification1c() method.
    }

    public function getExportFields1c($context = null)
    {
        // TODO: Implement getExportFields1c() method.
    }

    public static function getIdFieldName1c()
    {
        // TODO: Implement getIdFieldName1c() method.
    }

    public function getPrimaryKey()
    {
        // TODO: Implement getPrimaryKey() method.
    }
}
