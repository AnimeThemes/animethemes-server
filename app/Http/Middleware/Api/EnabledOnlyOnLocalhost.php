<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use App\Constants\FeatureConstants;
use Closure;
use Illuminate\Http\Request;
use Laravel\Pennant\Feature;

class EnabledOnlyOnLocalhost
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
        if (Feature::for(null)->active(FeatureConstants::ENABLED_ONLY_ON_LOCALHOST)) {
            $ip = $request->ip();

            if ($ip !== '127.0.0.1') {
                abort(403, 'Route only available for localhost');
            }
        }

        return $next($request);
    }
}
