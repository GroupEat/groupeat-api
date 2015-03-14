<?php

use Groupeat\Support\Http\V1\DebugController;

Route::group(['prefix' => 'api'], function () {
    Route::get('debug', DebugController::class.'@debug');
});
