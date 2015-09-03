<?php

use Groupeat\Support\Http\V1\PingController;

// There need to be at least one regular route to be able to use to cache the other API routes.
Route::post('/api/ping', PingController::class.'@ping');
