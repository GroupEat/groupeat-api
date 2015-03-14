<?php
namespace Groupeat\Support\Values;

use Groupeat\Support\Values\Abstracts\Value;

class Environment extends Value
{
    public function isLocal()
    {
        return $this->value == 'local';
    }

    public function is($environment)
    {
        return $this->value == $environment;
    }
}
