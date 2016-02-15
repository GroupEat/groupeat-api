<?php
namespace Groupeat\Messaging\Values;

use Groupeat\Support\Values\PhoneNumber;
use JsonSerializable;

class Sms implements JsonSerializable
{
    private $phoneNumber;
    private $text;

    public function __construct(PhoneNumber $phoneNumber, string $text)
    {
        $this->phoneNumber = $phoneNumber;
        $this->text = $text;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function getText()
    {
        return $this->text;
    }

    public function jsonSerialize()
    {
        return [
            'phoneNumber' => $this->phoneNumber,
            'text' => $this->text,
        ];
    }

    public function __toString(): string
    {
        return 'To: ' . $this->phoneNumber . ', text: "' . $this->text . '"';
    }
}
