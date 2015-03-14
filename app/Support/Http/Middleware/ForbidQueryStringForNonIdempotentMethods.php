<?php
namespace Groupeat\Support\Http\Middleware;

use Closure;
use Groupeat\Support\Exceptions\BadRequest;
use Illuminate\Http\Request;

class ForbidQueryStringForNonIdempotentMethods
{
    public function handle(Request $request, Closure $next)
    {
        if (!in_array($request->method(), ['GET', 'OPTIONS', 'HEAD'])) {
            $uriParts = explode('?', $request->getRequestUri());

            // The query string is everything after the interrogation mark
            if (!empty($uriParts[1])) {
                throw new BadRequest(
                    'queryStringForbiddenForCurrentHttpVerb',
                    "Cannot pass data in the query string outside of a GET, OPTIONS or HEAD request."
                );
            }
        }

        return $next($request);
    }
}
