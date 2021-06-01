<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class LoggerMiddleware
 * @package App\Http\Middleware
 */
class LoggerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        Log::info('Request Info', [
            'method' => $request->method(),
            'full-url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
        ]);

        return $next($request);
    }
}
