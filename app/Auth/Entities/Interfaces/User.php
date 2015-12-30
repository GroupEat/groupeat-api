<?php
namespace Groupeat\Auth\Entities\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface User
{
    public function credentials(): MorphOne;
}
