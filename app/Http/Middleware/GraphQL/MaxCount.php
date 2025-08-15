<?php

declare(strict_types=1);

namespace App\Http\Middleware\GraphQL;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class MaxCount
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): mixed  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        Config::set('graphql.pagination_values.default_count', $this->isLocal($request) ? 1000000 : 15);
        Config::set('graphql.pagination_values.max_count', $this->isLocal($request) ? null : 100);

        return $next($request);
    }

    /**
     * Determine if the request came from localhost.
     */
    private function isLocal(Request $request): bool
    {
        return $request->ip() === '127.0.0.1';
    }
}
