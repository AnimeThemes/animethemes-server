<?php

declare(strict_types=1);

namespace App\GraphQL\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class GraphQLPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): mixed  $next
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
