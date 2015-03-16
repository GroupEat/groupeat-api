<?php
namespace Groupeat\Auth\Commands;

use Groupeat\Support\Commands\Abstracts\Command;

class ActivateUser extends Command
{
    private $token;

    /**
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }
}
