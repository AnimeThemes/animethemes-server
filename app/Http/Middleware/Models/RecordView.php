<?php

declare(strict_types=1);

namespace App\Http\Middleware\Models;

use App\Constants\FeatureConstants;
use Closure;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Laravel\Pennant\Feature;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class RecordView
{
    /**
     * The viewable model.
     *
     * @var (Viewable&Model)|null
     */
    public $model = null;

    /**
     * @param  Closure(Request): mixed  $next
     */
    public function handle(Request $request, Closure $next, string $modelKey): mixed
    {
        $this->model = $request->route($modelKey);

        if (! $this->model instanceof Viewable) {
            throw new RuntimeException('record_view should only be configured for viewable models');
        }

        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        if (Feature::for(null)->active(FeatureConstants::ALLOW_VIEW_RECORDING) && $this->model instanceof Viewable) {
            views($this->model)->cooldown(now()->addMinutes(5))->record();
        }
    }
}
