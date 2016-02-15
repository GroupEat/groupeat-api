<?php
namespace Groupeat\Notifications\Values;

use Groupeat\Support\Values\Abstracts\SingleValue;

class ApnsCertificatePassphrase extends SingleValue
{
    public function value(): string
    {
        return $this->value;
    }
}
