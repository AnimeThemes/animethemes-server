<?php

declare(strict_types=1);

namespace App\Http\Middleware\GraphQL;

use Closure;
use Illuminate\Http\Request;

class RequiresContentType
{
    /**
     * @param  Closure(Request): mixed  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        abort_unless($request->isJson(), 422, 'Content-Type: application/json header is required.');

        return $next($request);
    }
}
