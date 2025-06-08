<?php

declare(strict_types=1);

namespace App\GraphQL\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class LogGraphQLRequest.
 */
class LogGraphQLRequest
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
        $requestId = Str::uuid()->__toString();

        Log::withContext([
            'request-id' => $requestId,
        ]);

        Log::info('GraphQL Request Info', [
            'method' => $request->method(),
            'full-url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
            'query' => json_encode($request->json()->all()),
        ]);

        $response = $next($request);

        if ($response instanceof Response) {
            $response->header('Request-Id', $requestId);
        }

        return $response;
    }
}
