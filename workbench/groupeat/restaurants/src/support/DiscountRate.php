<?php
namespace Groupeat\Restaurants\Support;

use Groupeat\Support\Exceptions\Exception;
use SebastianBergmann\Money\Money;

class DiscountRate
{
    /**
     * @var int Between 0 and 100
     */
    private $percentage;

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

    /**
     * @return int
     */
    public function toPercentage()
    {
        return $this->percentage;
    }

    /**
     * @param Money $price
     *
     * @return Money
     */
    public function applyTo(Money $price)
    {
        return $price->multiply(1 - $this->percentage / 100);
    }
}
