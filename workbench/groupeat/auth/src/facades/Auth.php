<?php namespace Groupeat\Auth\Facades;

use Illuminate\Support\Facades\Facade;

class Auth extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'groupeat.auth';
    }

}
