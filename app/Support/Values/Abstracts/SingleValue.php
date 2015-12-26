<?php
namespace Groupeat\Support\Values\Abstracts;

use JsonSerializable;

abstract class SingleValue implements JsonSerializable
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param mixed       $value
     * @param string|null $name
     */
    public function __construct($value, $name = null)
    {
        $this->value = $value;
        $this->name = $name;
    }

    public function value()
    {
        return $this->value;
    }

    public function name()
    {
        return $this->name;
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
