<?php

declare(strict_types=1);

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, ?string ...$guards): JsonResponse|Response|RedirectResponse
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($request->expectsJson()) {
                    return new JsonResponse([
                        'error' => 'Already authenticated.',
                    ]);
                }

                return redirect(Config::get('fortify.home'));
            }
        }

        return $next($request);
    }
}
