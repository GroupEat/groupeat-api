<?php
namespace Groupeat\Restaurants\Support;

use Groupeat\Support\Exceptions\Exception;
use SebastianBergmann\Money\Money;

class DiscountRate
{
    public function __construct($percentage)
    {
        if (!is_int($percentage)) {
            throw new Exception(
                'percentageMustBeAnInteger',
                "The percentage must be an integer."
            );
        }

        if ($percentage < 0 || $percentage > 100) {
            throw new Exception(
                'invalidPercentage',
                "The percentage must belong to [0, 100], $percentage given."
            );
        }

        $this->percentage = $percentage;
    }

    public function toPercentage(): int
    {
        return $this->percentage;
    }

    public function applyTo(Money $price): Money
    {
        return $price->multiply(1 - $this->percentage / 100);
    }
}
