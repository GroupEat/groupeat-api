<?php
namespace Groupeat\Support\Values\Abstracts;

abstract class DistanceInKms extends SingleValue
{
    public function value(): float
    {
        return $this->value;
    }
}
