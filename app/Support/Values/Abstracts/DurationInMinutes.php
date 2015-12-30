<?php
namespace Groupeat\Support\Values\Abstracts;

abstract class DurationInMinutes extends SingleValue
{
    public function value(): int
    {
        return $this->value;
    }
}
