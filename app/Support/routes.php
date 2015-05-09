<?php

use Groupeat\Support\Http\V1\PingController;

Route::group(['prefix' => 'api'], function () {
    Route::get('ping', PingController::class.'@ping');
});
