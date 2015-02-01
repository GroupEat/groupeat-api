<?php namespace Groupeat\Orders\Entities;

use Groupeat\Restaurants\Entities\ProductFormat;
use Groupeat\Support\Entities\Entity;

class Order extends Entity {

    public $timestamps = false;

    protected $dates = ['created_at'];


    public function getRules()
    {
        return [
            'customer_id' => 'required|integer',
            'group_order_id' => 'required|integer',
            'rawPrice' => 'required|numeric',
        ];
    }

    public function customer()
    {
        return $this->belongsTo('Groupeat\Customers\Entities\Customer');
    }

    public function groupOrder()
    {
        return $this->belongsTo('Groupeat\Orders\Entities\GroupOrder');
    }

    public function productFormats()
    {
        return $this->belongsToMany('Groupeat\Restaurants\Entities\ProductFormat')->withPivot('amount');
    }

    public function deliveryAddress()
    {
        return $this->hasOne('Groupeat\Orders\Entities\DeliveryAddress');
    }

    public function getReducedPriceAttribute()
    {
        return round((1 - $this->groupOrder->reduction) * $this->rawPrice, 2);
    }

    protected function setRawPriceAttribute($rawPrice)
    {
        $this->attributes['rawPrice'] = round($rawPrice, 2);
    }

}
