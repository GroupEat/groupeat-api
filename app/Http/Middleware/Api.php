<?php
namespace Groupeat\Http\Middleware;

use Auth;
use Closure;

class Api
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authorizationHeader = $request->header('authorization');

        if (!is_null($authorizationHeader)) {
            list($temp, $token) = explode(' ', $authorizationHeader);

            Auth::login($token);
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
