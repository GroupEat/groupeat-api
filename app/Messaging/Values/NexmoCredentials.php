<?php
namespace Groupeat\Messaging\Values;

class NexmoCredentials
{
    private $key;
    private $secret;

    public function __construct(NexmoKey $key, NexmoSecret $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getSecret()
    {
        return $this->secret;
    }
}
