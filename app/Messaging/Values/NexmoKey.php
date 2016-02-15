<?php
namespace Groupeat\Messaging\Values;

use Groupeat\Support\Values\Abstracts\SingleValue;

class NexmoKey extends SingleValue
{
    public function value(): string
    {
        return $this->value;
    }
}
