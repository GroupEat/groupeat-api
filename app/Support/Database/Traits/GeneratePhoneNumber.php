<?php
namespace Groupeat\Support\Database\Traits;

use Groupeat\Support\Values\PhoneNumber;

trait GeneratePhoneNumber
{
    protected function generatePhoneNumber()
    {
        $number = '33' . str_pad($this->faker->randomNumber(9), 9, '6', STR_PAD_LEFT);

        return new PhoneNumber($number);
    }
}
