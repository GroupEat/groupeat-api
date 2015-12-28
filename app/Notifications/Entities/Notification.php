<?php
namespace Groupeat\Notifications\Entities;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Notifications\Values\Notification as NoisyNotification;
use Groupeat\Notifications\Values\SilentNotification;
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

    /**
     * @param int         $timeToLiveInSeconds
     * @param array       $additionalData
     * @param string|null $title
     * @param string|null $message
     *
     * @return NoisyNotification|SilentNotification
     */
    public function toValue($timeToLiveInSeconds, $additionalData, $title, $message)
    {
        $additionalData = (array) $additionalData;
        $additionalData['notificationId'] = $this->id;

        if ($this->silent) {
            return new SilentNotification($this->device, $timeToLiveInSeconds, $additionalData);
        } else {
            return new NoisyNotification(
                $this->device,
                $title,
                $message,
                $timeToLiveInSeconds,
                $additionalData
            );
        }
    }
}
