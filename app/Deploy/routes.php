<?php

Route::group(['prefix' => 'api'], function () {
    Route::get('deploy/opcache/reset', 'Groupeat\Deploy\Http\V1\OpCacheController@reset');
});
