<?php

foreach([403 => 'Forbidden', 404 => 'Not found', 500 => 'Internal error', 503 => 'Maintenance'] as $code => $title)
{
    Route::get($code, ['as' => "errors.$code", function() use ($code, $title)
    {
        return Response::view('support::error', [
            'code' => $code,
            'title' => "$code: $title",
            'hideNavbar' => true,
        ], $code);
    }]);
}
