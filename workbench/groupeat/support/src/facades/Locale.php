<?php namespace Groupeat\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Locale extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'groupeat.locale';
    }

}
