<?php
namespace Groupeat\Notifications\Entities;

use Carbon\Carbon;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Entities\Abstracts\ImmutableDatedEntity;

class Notification extends ImmutableDatedEntity
{
    const RECEIVED_AT = 'receivedAt';

    protected $dates = [self::RECEIVED_AT];

    public function getRules()
    {
        return [
            'silent' => 'required|boolean',
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
