<?php
namespace Groupeat\Support\Values;

use Groupeat\Support\Values\Abstracts\SingleValue;

class NullValue extends SingleValue
{
    public function __construct()
    {
        parent::__construct(null);
    }
}
