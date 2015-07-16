<?php

use Groupeat\Support\Http\V1\PingController;

Route::group(['prefix' => 'api'], function () {
    Route::post('ping', PingController::class.'@ping');
});
