<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class WithoutTrashed
 * @package App\Http\Middleware
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

        if ($model == null || $model->trashed()) {
            return redirect(route('welcome'));
        }

        return $next($request);
    }
}
