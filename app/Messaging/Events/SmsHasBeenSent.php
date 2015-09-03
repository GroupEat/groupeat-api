<?php
namespace Groupeat\Messaging\Events;

use Groupeat\Messaging\Values\Sms;
use Groupeat\Support\Events\Abstracts\Event;

class SmsHasBeenSent extends Event
{
    private $sms;

    public function __construct(Sms $sms)
    {
        $this->sms = $sms;
    }

    public function getSms()
    {
        return $this->sms;
    }
}
