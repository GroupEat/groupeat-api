<?php

foreach ([403 => 'Forbidden', 404 => 'Not found', 500 => 'Internal error', 503 => 'Maintenance'] as $code => $title) {
    Route::get($code, ['as' => "errors.$code", function () use ($code, $title) {
        return Response::view('support::error', [
            'code' => $code,
            'title' => "$code: $title",
        ], $code);
    }]);

    // TODO: Remove this block before app launch
    Route::api(['version' => 'v1', 'protected' => false], function () {
        Route::get('log', function () {
            $data['data'] = [
                'url' => \Request::fullUrl(),
                'headers' => \Request::header(),
            ];

            \Log::debug($data['data']);

            return $data;
        });
    });
}
