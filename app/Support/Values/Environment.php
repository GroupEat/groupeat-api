<?php
namespace Groupeat\Support\Values;

use Groupeat\Support\Values\Abstracts\Value;

class Environment extends Value
{
    public function isLocal()
    {
        return $this->is('local');
    }

    public function isStaging()
    {
        return $this->is('staging');
    }

    public function isProduction()
    {
        return $this->is('production');
    }

    public function is($environment)
    {
        return $this->value == $environment;
    }
}
