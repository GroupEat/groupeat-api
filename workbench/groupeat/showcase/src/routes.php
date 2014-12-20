<?php

use Groupeat\Users\Entities\Address;

Route::get('/', function()
{
    ddump(Address::find(101)->user);

    return $addresses;
});
