<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogRequest
{
    /**
     * @param  Closure(Request): mixed  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $requestId = Str::uuid()->__toString();

        Log::withContext([
            'request-id' => $requestId,
        ]);

        Log::info('Request Info', [
            'method' => $request->method(),
            'full-url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
        ]);

        $response = $next($request);

        if ($response instanceof Response) {
            $response->header('Request-Id', $requestId);
        }

        return $response;
    }
}
