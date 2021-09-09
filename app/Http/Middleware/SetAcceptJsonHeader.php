<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class SetAcceptJsonHeader.
 */
class SetAcceptJsonHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
