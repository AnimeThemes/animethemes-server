<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Constants\Config\FlagConstants;
use Closure;
use Illuminate\Http\Request;

/**
 * Class IsVideoStreamingAllowed.
 */
class IsVideoStreamingAllowed
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (! config(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, false)) {
            return redirect(route('welcome'));
        }

        return $next($request);
    }
}
