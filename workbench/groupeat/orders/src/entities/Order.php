<?php namespace Groupeat\Orders\Entities;

use Groupeat\Support\Entities\Entity;

class Order extends Entity {

    public $timestamps = false;


    public function getRules()
    {
        return [
            'customer_id' => 'required|integer',
            'grouped_order_id' => 'required|integer',
        ];
    }

    public function customer()
    {
        return $this->belongsTo('Groupeat\Customers\Entities\Customer');
    }

    public function groupedOrder()
    {
        return $this->belongsTo('Groupeat\Orders\Entities\GroupedOrder');
    }

    public function productFormats()
    {
        return $this->belongsToMany('Groupeat\Restaurants\Entities\ProductFormat');
    }

    public function deliveryAddress()
    {
        return $this->hasOne('Groupeat\Orders\Entities\DeliveryAddress');
    }

    protected function updateTimestamps()
    {
        $time = $this->freshTimestamp();

        if ( ! $this->exists && ! $this->isDirty(static::CREATED_AT))
        {
            $this->setCreatedAt($time);
        }
    }

}
