<?php

namespace App\Http\Middleware;

use Closure;
use Enlightn\Enlightn\Analyzers\Concerns\DetectsRedis;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;

class ThrottleRequestsWithService
{
    use DetectsRedis;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int|string  $maxAttempts
     * @param  float|int  $decayMinutes
     * @param  string  $prefix
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        // Throttle with Redis if configured, else use default throttling middleware
        $middleware = null;
        if ($this->appUsesRedis()) {
            $middleware = app(ThrottleRequestsWithRedis::class);
        } else {
            $middleware = app(ThrottleRequests::class);
        }

        // Use named limiter if configured, else use default handling
        // Note: framework requires that we pass exactly 3 arguments to use named limiter
        if (app(RateLimiter::class)->limiter($maxAttempts) !== null) {
            return $middleware->handle($request, $next, $maxAttempts);
        }

        return $middleware->handle($request, $next, $maxAttempts, $decayMinutes, $prefix);
    }
}
