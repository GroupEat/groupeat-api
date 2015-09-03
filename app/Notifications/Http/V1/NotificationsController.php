<?php
namespace Groupeat\Notifications\Http\V1;

use Groupeat\Devices\Entities\Device;
use Groupeat\Notifications\Entities\Notification;
use Groupeat\Notifications\Services\SendNotification;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Http\V1\Abstracts\Controller;

class NotificationsController extends Controller
{
    public function send(GroupOrder $groupOrder, SendNotification $sendNotification)
    {
        $customer = $this->auth->customer();

        $devices = Device::where('customerId', $customer->id)->get();

        foreach ($devices as $device) {
            $notification = new Notification;
            $notification->customer()->associate($device->customer);
            $notification->device()->associate($device);
            $notification->groupOrder()->associate($groupOrder);

            $forceOnDev = true;
            $sendNotification->call($notification, $forceOnDev);
        }
    }
}
