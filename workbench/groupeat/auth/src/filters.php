<?php

// Forbid authentication token in query string for security reasons
Route::before(function ($request) {
    if (Route::isApiRequest($request) && $request->get('token')) {
        throw new \Groupeat\Support\Exceptions\BadRequest(
            'authenticationTokenInQueryStringForbidden',
            "Trying to authenticate via token in query string is forbidden."
        );
    }
});
