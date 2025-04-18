<?php

declare(strict_types=1);

namespace App\GraphQL\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

/**
 * Class GraphQLPolicy.
 */
class GraphQLPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): mixed  $next
     * @param  string  $modelKey
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        Gate::guessPolicyNamesUsing(
            fn (string $modelClass) => Str::of($modelClass)
                ->replace('Models', 'GraphQL\\Policies')
                ->append('Policy')
                ->__toString()
        );

        return $next($request);
    }
}
