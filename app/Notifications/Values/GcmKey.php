<?php
namespace Groupeat\Notifications\Values;

use Groupeat\Support\Values\Abstracts\SingleValue;

class GcmKey extends SingleValue
{
    public function value(): string
    {
        return $this->value;
    }
}
