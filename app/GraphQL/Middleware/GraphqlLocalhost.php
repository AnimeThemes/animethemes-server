<?php

declare(strict_types=1);

namespace App\GraphQL\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class GraphqlLocalhost.
 */
class GraphqlLocalhost
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): mixed  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $ip = $request->ip();

        if ($ip !== '127.0.0.1' && app()->isProduction()) {
            abort(403, "GraphQL is only enabled for localhost");
        }

        return $next($request);
    }
}
