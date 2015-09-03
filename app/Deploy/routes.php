<?php

use Groupeat\Deploy\Http\V1\OpCacheController;

$api->version('v1', function ($api) {
    $api->delete('deploy/opcache', OpCacheController::class.'@clear');
});
