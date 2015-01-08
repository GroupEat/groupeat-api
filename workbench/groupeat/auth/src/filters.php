<?php

use Symfony\Component\HttpFoundation\Response;

// Forbid authentication token in query string for security reasons
Route::before(function($request)
{
    if ($request->get('token'))
    {
        return App::abort(
            Response::HTTP_FORBIDDEN,
            "Trying to authenticate via token in query string is forbidden."
        );
    }
});
