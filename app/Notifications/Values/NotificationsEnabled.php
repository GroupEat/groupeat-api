<?php
namespace Groupeat\Notifications\Values;

use Groupeat\Support\Values\Abstracts\SingleValue;

class NotificationsEnabled extends SingleValue
{
    public function value(): bool
    {
        return $this->value;
    }
}
