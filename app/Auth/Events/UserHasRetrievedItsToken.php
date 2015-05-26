<?php
namespace Groupeat\Auth\Events;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Support\Events\Abstracts\Event;

class UserHasRetrievedItsToken extends Event
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
