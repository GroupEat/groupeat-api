<?php

// Send CORS headers on API requests in order to grant access to the mobile app
Route::after(function ($request, $response) {
    if (Route::isApiRequest($request)) {
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, Accept, Authorization, X-Request-With',
        ];

        $replace = true;

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value, $replace);
        }
    }
});

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

// CSRF Protection Filter
Route::filter('csrf', function () {
    if (Session::token() !== Input::get('_token')) {
        throw new Illuminate\Session\TokenMismatchException;
    }
});

// TODO: fix error responses
// Register error handler
App::down(function () {
    return Response::view('support::error', [
        'code' => 503,
        'title' => "503: Maintenance",
    ], 503);
});

App::error(function ($exception, $code) {
    if (!Config::get('app.debug')) {
        Log::error($exception);

        if (in_array($code, [404, 403])) {
            return Redirect::to($code);
        }

        return Redirect::to('500');
    }
});
