<?php
namespace Groupeat\Orders\Entities;

use Groupeat\Support\Entities\Abstracts\Address as AbstractAddress;

class DeliveryAddress extends AbstractAddress
{
    public $timestamps = false;

    public function getRules()
    {
        $rules = parent::getRules();

        $rules['orderId'] = 'required|integer';

        return $rules;
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
