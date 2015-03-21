<?php
namespace Groupeat\Notifications\Http\V1;

use Groupeat\Notifications\Entities\Device;
use Groupeat\Notifications\Services\SendGcmNotification;
use Groupeat\Support\Http\V1\Abstracts\Controller;

class NotificationsController extends Controller
{
    public function send(SendGcmNotification $sendGcmNotification)
    {
        return $sendGcmNotification->call($this->auth->customer());
    }
}
