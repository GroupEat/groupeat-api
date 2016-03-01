<?php
namespace Groupeat\Restaurants\Entities;

use Groupeat\Support\Entities\Abstracts\Entity;
use SebastianBergmann\Money\EUR;
use SebastianBergmann\Money\Money;

class Promotion extends Entity
{
    public $timestamps = false;
    protected $table = 'restaurant_promotions';

    public function getRules()
    {
        return [
            'restaurantId' => 'required',
            'rawPriceThreshold' => 'required|integer',
            'beneficiaryCount' => 'integer',
            'name' => 'required|string',
        ];
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    protected function getRawPriceThresholdAttribute(): Money
    {
        return new EUR($this->attributes['rawPriceThreshold']);
    }
}
