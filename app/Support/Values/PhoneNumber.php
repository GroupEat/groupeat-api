<?php
namespace Groupeat\Support\Values;

use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Values\Abstracts\Value;

class PhoneNumber extends Value
{
    const REGEX = '/^33(\d)(\d{2})(\d{2})(\d{2})(\d{2})$/';

    public function __construct($number)
    {
        static::assertValid($number);

        parent::__construct($number);
    }

    /**
     * @param string $string
     *
     * @return static
     */
    public static function from($string)
    {
        $string = preg_replace('/\D/', '', $string);

        if (starts_with($string, '0')) {
            $string = '33' . ltrim($string, '0');
        } elseif (starts_with($string, '330')) {
            $string = '33' . ltrim($string, '330');
        }

        return new static($string);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        preg_match(static::REGEX, $this->value, $matches);
        array_shift($matches);

        return '0' . implode(' ', $matches);
    }

    private static function assertValid($number)
    {
        if (!preg_match(static::REGEX, $number)) {
            throw new BadRequest(
                'badPhoneNumberFormat',
                'The phone number must match ' . static::REGEX . ', "' . $number . '" given.'
            );
        }
    }
}