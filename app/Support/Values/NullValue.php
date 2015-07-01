<?php
namespace Groupeat\Support\Values;

use Groupeat\Support\Values\Abstracts\Value;

class NullValue extends Value
{
    public function __construct()
    {
        parent::__construct(null);
    }
}
