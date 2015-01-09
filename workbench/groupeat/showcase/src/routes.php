<?php

Route::get('/', ['as' => 'home', function()
{
    return View::make('showcase::index', ['hideNavbar' => true]);
}]);
