<?php
namespace Groupeat\Restaurants\Support;

use Groupeat\Support\Exceptions\Exception;
use SebastianBergmann\Money\Money;

class DiscountRate
{
    /**
     * The discount policy of a restaurant is saved in the database as an
     * array that represent the price to reach to unlock a specific discount rate.
     *
     * Example: if the array below is [0, 10, 20, 30, 40, 50] and the restaurant
     * discount prices are [900, 1000, 2000, 2500, 3500, 6000], it means that for 10e there
     * will be a 10% discount, for 20e 20%, for 25e 30%, for 35e 40% and for 60e 50%.
     * From 0e to 9e there won't be any discount. Between the given points, the discount
     * rate increase linearly.
     */
    const PERCENTAGES = [0, 10, 20, 30, 40, 50];

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
