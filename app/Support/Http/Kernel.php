<?php
namespace Groupeat\Support\Http;

use Clockwork\Support\Laravel\ClockworkMiddleware;
use Groupeat\Auth\Http\Middleware\AllowDifferentToken;
use Groupeat\Auth\Http\Middleware\Authenticate;
use Groupeat\Auth\Http\Middleware\ForbidTokenInQueryString;
use Groupeat\Support\Http\Middleware\Api;
use Groupeat\Support\Http\Middleware\ForbidQueryStringForNonIdempotentMethods;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Routing\Router;

class Kernel extends HttpKernel
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

    public function __construct(Application $app, Router $router)
    {
        parent::__construct($app, $router);

        $clockworkMiddlewareClass = ClockworkMiddleware::class;

        if (class_exists($clockworkMiddlewareClass)) {
            $this->pushMiddleware($clockworkMiddlewareClass);
        }
    }

}
