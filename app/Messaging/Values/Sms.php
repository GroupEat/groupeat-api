<?php
namespace Groupeat\Messaging\Values;

use Groupeat\Support\Values\PhoneNumber;
use JsonSerializable;

class Sms implements JsonSerializable
{
    /**
     * @var PhoneNumber
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $text;

    public function __construct(PhoneNumber $phoneNumber, $text)
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

    public function __toString()
    {
        return 'To: ' . $this->phoneNumber . ', text: "' . $this->text . '"';
    }
}
