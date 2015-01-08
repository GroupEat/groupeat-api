<?php

Route::get('/', function()
{
    return View::make('showcase::index', ['hideNavbar' => true]);
});
