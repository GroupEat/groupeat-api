<?php
namespace Groupeat\Support\Values\Abstracts;

use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use ReflectionClass;
use ReflectionProperty;

abstract class Activity implements Arrayable, Jsonable, JsonSerializable
{
    const SENSITIVE_ATTRIBUTES = ['password', 'token'];

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function __toString()
    {
        return get_class();
    }

    public function toArray()
    {
        $attributes = [];

        collect((new ReflectionClass(static::class))->getProperties())
            ->each(function (ReflectionProperty $reflectionAttribute) use (&$attributes) {
                $name = $reflectionAttribute->getName();
                $reflectionAttribute->setAccessible(true);
                $value = $reflectionAttribute->getValue($this);

                if ($value instanceof Entity) {
                    $value = $value->getKey();
                } else {
                    $isSensitive = !empty(collect(self::SENSITIVE_ATTRIBUTES)->first(function ($_, $attribute) use ($name) {
                        return str_contains(strtolower($name), $attribute);
                    }));

                    if ($isSensitive) {
                        $value = '***hidden***';
                    }
                };

                $attributes[$name] = $value;
            });

        return $attributes;
    }
}
