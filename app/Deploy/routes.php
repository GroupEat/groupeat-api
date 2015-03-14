<?php

use Groupeat\Deploy\Http\V1\OpCacheController;

Route::group(['prefix' => 'api'], function () {
    Route::get('deploy/opcache/reset', OpCacheController::class.'@reset');
});
