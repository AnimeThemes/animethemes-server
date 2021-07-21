<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\BaseModel;
use Closure;
use Illuminate\Http\Request;

/**
 * Class WithoutTrashed.
 */
class WithoutTrashed
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $modelKey
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $modelKey): mixed
    {
        $model = $request->route($modelKey);

        if (! $model instanceof BaseModel || $model->trashed()) {
            return redirect(route('welcome'));
        }

        return $next($request);
    }
}
