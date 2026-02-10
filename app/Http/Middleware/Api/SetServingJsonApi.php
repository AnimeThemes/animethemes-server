<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;

class SetServingJsonApi
{
    public static bool $isServing = false;

    /**
     * @param  Closure(Request): mixed  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        static::$isServing = true;

        return $next($request);
    }
}
