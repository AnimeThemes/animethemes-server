<?php

declare(strict_types=1);

namespace App\Http\Middleware\GraphQL;

use Closure;
use GraphQL\Validator\Rules\QueryComplexity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class MaxCount
{
    /**
     * @param  Closure(Request): mixed  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        Config::set('lighthouse.pagination.default_count', $this->isLocal($request) ? 1000000 : 15);
        Config::set('lighthouse.pagination.max_count', $this->isLocal($request) ? null : 100);

        Config::set('lighthouse.security.max_query_complexity', $this->isLocal($request) ? QueryComplexity::DISABLED : 10000);

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
