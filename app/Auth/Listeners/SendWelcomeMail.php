<?php
namespace Groupeat\Auth\Listeners;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Events\UserHasRegistered;
use Groupeat\Mailing\Services\SendMail;
use Groupeat\Support\Listeners\Abstracts\QueuedListener;

class SendWelcomeMail extends QueuedListener
{
    private $mailer;

    public function __construct(SendMail $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(UserHasRegistered $event)
    {
        $this->mailer->call(
            $event->getUser(),
            'auth::welcome',
            'auth::welcome.subject'
        );
    }
}
