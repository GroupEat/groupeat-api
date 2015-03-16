<?php
namespace Groupeat\Support\Kernels;

use Groupeat\Auth\Http\Middleware\AllowDifferentToken;
use Groupeat\Auth\Http\Middleware\Authenticate;
use Groupeat\Auth\Http\Middleware\ForbidTokenInQueryString;
use Groupeat\Support\Http\Middleware\Api;
use Groupeat\Support\Http\Middleware\ForbidQueryStringForNonIdempotentMethods;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;

class Http extends Kernel
{
    protected $middleware = [
        CheckForMaintenanceMode::class,
        Api::class,
        ForbidTokenInQueryString::class,
        ForbidQueryStringForNonIdempotentMethods::class,
    ];

    protected $routeMiddleware = [
        'allowDifferentToken' => AllowDifferentToken::class,
        'auth' => Authenticate::class,
    ];
}
