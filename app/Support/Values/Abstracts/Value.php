<?php
namespace Groupeat\Support\Values\Abstracts;

use JsonSerializable;

abstract class Value implements JsonSerializable
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->value;
    }
}
