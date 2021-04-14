<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WithoutTrashed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $modelKey
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $modelKey)
    {
        $model = $request->route($modelKey);

        if ($model == null || $model->trashed()) {
            return redirect(route('welcome.index'));
        }

        return $next($request);
    }
}
