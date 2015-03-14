<?php
namespace Groupeat\Auth\Http\Middleware;

use Closure;
use Groupeat\Auth\Auth;
use Illuminate\Http\Request;

class Authenticate
{
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        $this->auth->checkOrFail();

        return $next($request);
    }
}
