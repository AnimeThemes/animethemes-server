<?php

namespace App\Http\Middleware;

use Closure;
use Enlightn\Enlightn\Analyzers\Concerns\DetectsRedis;
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
        if ($this->appUsesRedis()) {
            return app(ThrottleRequestsWithRedis::class)->handle($request, $next, $maxAttempts, $decayMinutes, $prefix);
        }

        return app(ThrottleRequests::class)->handle($request, $next, $maxAttempts, $decayMinutes, $prefix);
    }
}
