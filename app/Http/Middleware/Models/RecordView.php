<?php

declare(strict_types=1);

namespace App\Http\Middleware\Models;

use App\Constants\Config\FlagConstants;
use Closure;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use RuntimeException;

/**
 * Class RecordView.
 */
abstract class RecordView
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
        return $next($request);
    }

    /**
     * Perform any final actions for the request lifecycle.
     *
     * @param Request $request
     * @return void
     */
    public function terminate(Request $request): void
    {
        $model = $request->route($this->key());

        if ($model instanceof Viewable && Config::bool(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED)) {
            views($model)->cooldown(now()->addMinutes(5))->record();
        }
    }

    /**
     * Get the route model binding key for the viewable object.
     *
     * @return string
     */
    abstract protected function key(): string;
}
