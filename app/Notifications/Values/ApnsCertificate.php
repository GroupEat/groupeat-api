<?php
namespace Groupeat\Notifications\Values;

class ApnsCertificate
{
    private $path;
    private $passphrase;

    public function __construct($path, $passphrase)
    {
        $this->path = $path;
        $this->passphrase = $passphrase;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getPassphrase()
    {
        return $this->passphrase;
    }
}
