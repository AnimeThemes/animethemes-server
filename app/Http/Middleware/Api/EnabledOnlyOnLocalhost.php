<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;

/**
 * Class EnabledOnlyOnLocalhost.
 */
class EnabledOnlyOnLocalhost
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

        if ($ip !== '127.0.0.1') {
            abort(403, "Route only available for localhost");
        }

        return $next($request);
    }
}
