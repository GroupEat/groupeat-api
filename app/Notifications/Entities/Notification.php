<?php
namespace Groupeat\Notifications\Entities;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Entities\Abstracts\ImmutableDatedEntity;

class Notification extends ImmutableDatedEntity
{
    public function getRules()
    {
        return [
            'customerId' => 'required',
            'deviceId' => 'required',
            'groupOrderId' => 'required'
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function groupOrder()
    {
        return $this->belongsTo(GroupOrder::class);
    }
}
