<?php
namespace Groupeat\Notifications\Http\V1;

use Auth;
use Groupeat\Notifications\Entities\Device;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use Input;
use Sly\NotificationPusher\Adapter\Gcm;
use Sly\NotificationPusher\Collection\DeviceCollection;
use Sly\NotificationPusher\Model\Device as NotifiedDevice;
use Sly\NotificationPusher\Model\Message;
use Sly\NotificationPusher\Model\Push;
use Sly\NotificationPusher\PushManager;

class NotificationsController extends Controller
{
    public function saveRegistrationId()
    {
        $device = new Device;
        $device->customer_id = Auth::user()->id;
        $device->device_id = Input::json('registrationId');

        $device->save();

        return "Device $device->device_id saved for customer $device->customer_id.";
    }

    public function sendNotification()
    {
        $customerId = Auth::user()->id;
        $deviceId = Device::where('customer_id', $customerId)->first()->device_id;

        if (empty($deviceId)) {
            return "Cannot find deviceId for customer $customerId.";
        }

        $pushManager = new PushManager(PushManager::ENVIRONMENT_DEV);

        $gcmAdapter = new Gcm([
            'apiKey' => 'AIzaSyBjyfcgeWD4UlHxANBs5-6rupcGp0_u1V0',
        ]);

        $devices = new DeviceCollection([new NotifiedDevice($deviceId)]);

        $message = new Message('This is an example.');

        $push = new Push($gcmAdapter, $devices, $message);
        $pushManager->add($push);
        $pushManager->push();

        foreach ($pushManager as $item) {
            var_dump($item->getAdapter()->getResponse());
        }
    }
}
