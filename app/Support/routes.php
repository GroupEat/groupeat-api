<?php

// TODO: Remove this block before app launch
Route::group(['prefix' => 'api', 'middleware' => 'auth'], function () {
    Route::get('log', function () {
        $data['data'] = [
            'url' => \Request::fullUrl(),
            'headers' => \Request::header(),
        ];

        \Log::debug($data['data']);

        return $data;
    });
});
