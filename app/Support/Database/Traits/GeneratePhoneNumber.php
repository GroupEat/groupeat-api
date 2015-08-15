<?php
namespace Groupeat\Support\Database\Traits;

use Groupeat\Support\Values\PhoneNumber;

trait GeneratePhoneNumber
{
    protected function generatePhoneNumber()
    {
        $number = $this->faker->randomNumber(9);

        if ($number < 100000000) {
            $number *= 10;
        }

        return new PhoneNumber('33' . $number);
    }
}
