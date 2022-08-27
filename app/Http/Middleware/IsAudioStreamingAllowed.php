<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Constants\Config\FlagConstants;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class IsAudioStreamingAllowed.
 */
class IsAudioStreamingAllowed
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): mixed  $next
     * @return mixed
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (! Config::bool(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED)) {
            abort(403, 'Audio Streaming Disabled');
        }

        return $next($request);
    }
}
