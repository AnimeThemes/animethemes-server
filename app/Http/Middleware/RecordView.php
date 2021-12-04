<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Constants\Config\FlagConstants;
use Closure;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Http\Request;

/**
 * Class RecordView.
 */
class RecordView
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @param  string  $modelKey
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $modelKey): mixed
    {
        $model = $request->route($modelKey);

        if ($model instanceof Viewable && config(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED, false)) {
            views($model)->cooldown(now()->addMinutes(5))->record();
        }

        return $next($request);
    }
}
