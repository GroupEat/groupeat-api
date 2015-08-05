<?php
namespace Groupeat\Messaging\Values;

use JsonSerializable;

class Sms implements JsonSerializable
{
    /**
     * @var int
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $text;

    public function __construct($phoneNumber, $text)
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
}
