<?php

use Groupeat\Users\Entities\Address;

Route::get('/', function()
{
    return View::make('showcase::index');
});
