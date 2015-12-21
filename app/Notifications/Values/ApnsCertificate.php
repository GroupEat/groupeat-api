<?php
namespace Groupeat\Notifications\Values;

class ApnsCertificate
{
    private $path;
    private $passphrase;

    public function __construct(ApnsCertificatePath $path, ApnsCertificatePassphrase $passphrase)
    {
        $this->path = $path->value();
        $this->passphrase = $passphrase->value();
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
