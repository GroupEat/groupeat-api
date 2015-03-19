<?php
namespace Groupeat\Notifications\Http\V1;

use Groupeat\Notifications\Entities\Device;
use Groupeat\Notifications\Services\SendGcmNotification;
use Groupeat\Support\Http\V1\Abstracts\Controller;

class NotificationsController extends Controller
{
    public function saveRegistrationId()
    {
        $device = new Device;
        $device->customer_id = $this->auth->userId();
        $device->device_id = $this->json('registrationId');

        $device->save();

        return "Device $device->device_id saved for customer $device->customer_id.";
    }

    public function sendNotification(SendGcmNotification $sendGcmNotification)
    {
        return $sendGcmNotification->call($this->auth->customer());
    }
}
