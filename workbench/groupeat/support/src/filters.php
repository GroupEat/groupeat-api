<?php

// Send CORS headers on API requests in order to grant access to the mobile app
Route::after(function($request, $response)
{
    $apiPrefix = '/'.Config::get('api::prefix').'/';

    if (starts_with($request->getPathInfo(), $apiPrefix))
    {
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, Accept, Authorization, X-Request-With',
            'Access-Control-Allow-Credentials' => 'true',
        ];

        $replace = true;

        foreach ($headers as $key => $value)
        {
            $response->headers->set($key, $value, $replace);
        }
    }
});

// CSRF Protection Filter
Route::filter('csrf', function()
{
    if (Session::token() !== Input::get('_token'))
    {
        throw new Illuminate\Session\TokenMismatchException;
    }
});
