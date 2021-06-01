<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Class IsVideoStreamingAllowed.
 */
class IsVideoStreamingAllowed
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (! Config::get('app.allow_video_streams', false)) {
            return redirect(route('welcome'));
        }

        return $next($request);
    }
}
