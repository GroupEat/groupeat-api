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
        ];
    }

    /**
     * @return int The total price of the order without taking the possible reduction into account.
     */
    public function rawPrice()
    {
        $price = 0;

        foreach ($this->productFormats as $productFormat)
        {
            $price += $productFormat->pivot->amount * $productFormat->price;
        }

        return $price;
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

}
