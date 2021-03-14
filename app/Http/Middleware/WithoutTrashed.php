<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

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
            return redirect(Config::get('app.url'));
        }

        return $next($request);
    }
}
