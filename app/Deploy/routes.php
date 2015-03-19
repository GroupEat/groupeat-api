<?php

use Groupeat\Deploy\Http\V1\OpCacheController;

Route::group(['prefix' => 'api'], function () {
    Route::delete('deploy/opcache', OpCacheController::class.'@clear');
});
