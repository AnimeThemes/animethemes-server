<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\BaseModel;
use Closure;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Class WithoutTrashed.
 */
class WithoutTrashed
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

        if (! $model instanceof BaseModel) {
            throw new RuntimeException('without_trashed should only be configured for models that can be soft deleted');
        }

        if ($model->trashed()) {
            abort(403, 'Deleted Model');
        }

        return $next($request);
    }
}
