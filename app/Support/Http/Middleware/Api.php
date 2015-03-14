<?php
namespace Groupeat\Support\Http\Middleware;

use Closure;
use Groupeat\Auth\Auth;
use Illuminate\Http\Request;

class Api
{
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        $authorizationHeader = $request->header('authorization');

        if (!is_null($authorizationHeader)) {
            list($temp, $token) = explode(' ', $authorizationHeader);

            $this->auth->login($token);
        }

        $response = $next($request);

        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, Accept, Authorization, X-Request-With',
        ];

        $replace = true;

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value, $replace);
        }

        return $response;
    }
}
