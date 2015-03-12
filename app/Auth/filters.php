<?php

// Forbid authentication token in query string for security reasons
Route::before(function ($request) {
    if (str_contains($request->fullUrl(), 'token=')) {
        throw new \Groupeat\Support\Exceptions\BadRequest(
            'authenticationTokenInQueryStringForbidden',
            "Trying to authenticate via token in query string is forbidden."
        );
    }
});
