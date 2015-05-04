<?php
namespace Groupeat\Notifications\Entities;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Entities\Abstracts\Entity;

class Notification extends Entity
{
    public $timestamps = false;

    protected $dates = ['createdAt'];

    public function getRules()
    {
        return [
            'customerId' => 'required',
            'deviceId' => 'required',
            //'groupOrderId' => 'required', TODO: uncomment when test route is not needed anymore
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
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
