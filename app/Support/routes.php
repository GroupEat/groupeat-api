<?php

use Groupeat\Support\Http\V1\PingController;

$api->version('v1', function ($api) {
    $api->post('ping', PingController::class.'@ping');
});
