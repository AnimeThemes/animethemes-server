<?php

declare(strict_types=1);

namespace App\Http\Middleware\Models;

use App\Constants\FeatureConstants;
use App\Models\Service\ViewAggregate;
use Closure;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Laravel\Pennant\Feature;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RecordView.
 */
class RecordView
{
    /**
     * The viewable model
     *
     * @var (Viewable&Model)|null
     */
    public $model = null;

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
        $this->model = $request->route($modelKey);

        if (! $this->model instanceof Viewable) {
            throw new RuntimeException('record_view should only be configured for viewable models');
        }

        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     *
     * @param  Request  $request
     * @param  Response  $response
     * @return void
     */
    public function terminate(Request $request, Response $response): void
    {
        if (Feature::for(null)->active(FeatureConstants::ALLOW_VIEW_RECORDING)) {
            views($this->model)->cooldown(now()->addMinutes(5))->record();
            defer(fn () => ViewAggregate::query()->whereMorphedTo('viewAggregate', $this->model)->increment(ViewAggregate::ATTRIBUTE_VALUE));
        }
    }
}
