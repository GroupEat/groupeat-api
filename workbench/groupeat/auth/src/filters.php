<?php

use Symfony\Component\HttpFoundation\Response;

// Forbid authentication token in query string for security reasons
Route::before(function($request)
{
    if ($request->get('token'))
    {
        throw new \Groupeat\Support\Exceptions\BadRequest(
            'authenticationTokenInQueryStringForbidden',
            "Trying to authenticate via token in query string is forbidden."
        );
    }
});
