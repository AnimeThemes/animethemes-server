<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

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
     * @param string $modelKey
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $modelKey): mixed
    {
        $model = $request->route($modelKey);

        if ($model instanceof Viewable && Config::get('flags.allow_view_recording', false)) {
            views($model)->cooldown(now()->addMinutes(5))->record();
        }

        return $next($request);
    }
}
