<?php
namespace Groupeat\Support\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Api
{
    public function handle(Request $request, Closure $next)
    {
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
