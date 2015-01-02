<?php

Route::get('/', function()
{
    \Groupeat\Customers\Entities\Customer::all();

    return View::make('showcase::index');
});
