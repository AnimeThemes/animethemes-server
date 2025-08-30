<?php

declare(strict_types=1);

namespace App\Http\Middleware\GraphQL;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;

class SetServingGraphQL
{
    /**
     * @param  Closure(Request): mixed  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        Context::add('serving-graphql', true);

        return $next($request);
    }
}
