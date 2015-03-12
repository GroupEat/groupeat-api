<?php

// Allow query string for GET, OPTIONS and HEAD requests only
Route::before(function ($request) {
    if (!in_array($request->method(), ['GET', 'OPTIONS', 'HEAD'])) {
        $uriParts = explode('?', $request->getRequestUri());

        // The query string is everything after the interrogation mark
        if (!empty($uriParts[1])) {
            throw new \Groupeat\Support\Exceptions\BadRequest(
                'queryStringForbiddenForCurrentHttpVerb',
                "Cannot pass data in the query string outside of a GET, OPTIONS or HEAD request."
            );
        }
    }
});
