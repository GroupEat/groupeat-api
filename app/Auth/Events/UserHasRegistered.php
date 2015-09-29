<?php
namespace Groupeat\Auth\Events;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Events\Abstracts\Event;

class UserHasRegistered extends Event
{
    private $user;

    public function __construct(UserCredentials $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function __toString()
    {
        return $this->user->user->toShortString() . ' ('. $this->user->email .') has registered';
    }
}
