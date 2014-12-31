<?php

use Groupeat\Customers\Entities\Customer;

Route::get('/', function()
{
    Mail::send('showcase::mail', [], function($message)
    {
        $message->to('tib.dex@gmail.com', 'Tib Dex')->subject('salut!');
    });

    return View::make('showcase::index');
});
