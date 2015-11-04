<?php
namespace Groupeat\Support\Values\Abstracts;

use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use ReflectionClass;
use ReflectionMethod;

abstract class Activity implements Arrayable, Jsonable, JsonSerializable
{
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
        $getMethodPrefix = 'get';

        $methodNames = collect((new ReflectionClass(static::class))->getMethods())
            ->filter(function (ReflectionMethod $reflectionMethod) {
                return $reflectionMethod->isPublic();
            })
            ->map(function (ReflectionMethod $reflectionMethod) {
                return $reflectionMethod->name;
            })
            ->filter(function ($methodName) use ($getMethodPrefix) {
                return starts_with($methodName, $getMethodPrefix);
            });

        $attributes = [];

        foreach ($methodNames as $methodName) {
            $name = lcfirst(substr($methodName, strlen($getMethodPrefix)));
            $value = $this->$methodName();

            if ($value instanceof Entity) {
                $value = $value->getKey();
            } elseif (str_contains(strtolower($name), 'password') || str_contains(strtolower($name), 'token')) {
                $value = '***hidden***';
            }

            $attributes[$name] = $value;
        }

        return $attributes;
    }
}
