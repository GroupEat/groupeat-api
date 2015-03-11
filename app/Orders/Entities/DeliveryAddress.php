<?php
namespace Groupeat\Orders\Entities;

use Groupeat\Support\Entities\Abstracts\Address as AbstractAddress;

class DeliveryAddress extends AbstractAddress
{
    public $timestamps = false;

    public function getRules()
    {
        $rules = parent::getRules();

        $rules['order_id'] = 'required|integer';

        return $rules;
    }

    public function order()
    {
        return $this->belongsTo('Groupeat\Orders\Entities\Order');
    }
}
