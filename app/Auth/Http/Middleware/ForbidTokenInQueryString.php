<?php
namespace Groupeat\Auth\Http\Middleware;

use Closure;
use Groupeat\Support\Exceptions\BadRequest;
use Illuminate\Http\Request;

class ForbidTokenInQueryString
{
    public function handle(Request $request, Closure $next)
    {
        if (str_contains($request->fullUrl(), 'token=')) {
            throw new BadRequest(
                'authenticationTokenInQueryStringForbidden',
                "Trying to authenticate via token in query string is forbidden."
            );
        }

        return $next($request);
    }
}
