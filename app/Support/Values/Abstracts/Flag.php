<?php
namespace Groupeat\Support\Values\Abstracts;

abstract class Flag extends SingleValue
{
    public function value(): bool
    {
        return $this->value;
    }
}
