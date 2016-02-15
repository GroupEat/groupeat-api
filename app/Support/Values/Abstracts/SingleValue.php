<?php
namespace Groupeat\Support\Values\Abstracts;

use JsonSerializable;

abstract class SingleValue implements JsonSerializable
{
    /**
     * @var mixed
     */
    protected $value;
    protected $name;

    public function __construct($value, string $name = '')
    {
        $this->value = $value;
        $this->name = $name;
    }

    abstract public function value();

    public function name(): string
    {
        return $this->name;
    }

    public function __toString(): string
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
