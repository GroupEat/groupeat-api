<?php
namespace Groupeat\Admin\Http\V1;

use Groupeat\Admin\Entities\Admin;
use Groupeat\Devices\Entities\Device;
use Groupeat\Documentation\Services\GenerateApiDocumentation;
use Groupeat\Notifications\Services\SendNotification;
use Groupeat\Notifications\Values\Notification;
use Groupeat\Notifications\Values\SilentNotification;
use Groupeat\Support\Http\V1\Abstracts\Controller;

class AdminController extends Controller
{
    public function docs(GenerateApiDocumentation $generateApiDocumentation)
    {
        $this->auth->assertSameType(new Admin);

        return $generateApiDocumentation->getHTML();
    }

    public function sendNotification(Device $device, SendNotification $sendNotification)
    {
        $this->auth->assertSameType(new Admin);

        $title = $this->optionalJson('title');
        $message = $this->optionalJson('message');

        if ($title || $message) {
            $notification = new Notification(
                $device,
                $this->json('timeToLiveInSeconds'),
                $this->json('additionalData'),
                $title,
                $message
            );
        } else {
            $notification = new SilentNotification(
                $device,
                $this->json('timeToLiveInSeconds'),
                $this->json('additionalData')
            );
        }

        return $sendNotification->call($notification, true);
    }
}
