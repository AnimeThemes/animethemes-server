<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Constants\Config\FlagConstants;
use Closure;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use RuntimeException;

/**
 * Class RecordView.
 */
class RecordView
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): mixed  $next
     * @param  string  $modelKey
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $modelKey): mixed
    {
        $model = $request->route($modelKey);

        if (! $model instanceof Viewable) {
            throw new RuntimeException('record_view should only be configured for viewable models');
        }

        if (Config::bool(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED)) {
            views($model)->cooldown(now()->addMinutes(5))->record();
        }

        return $next($request);
    }
}
