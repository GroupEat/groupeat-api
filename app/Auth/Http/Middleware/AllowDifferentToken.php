<?php
namespace Groupeat\Auth\Http\Middleware;

use Closure;
use Groupeat\Auth\Auth;
use Illuminate\Http\Request;

class AllowDifferentToken
{
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        $this->auth->allowDifferentToken(true);

        $response = $next($request);

        $this->auth->allowDifferentToken(false);

        return $response;
    }
}
