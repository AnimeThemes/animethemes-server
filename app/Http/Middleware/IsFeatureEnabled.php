<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class IsFeatureEnabled.
 */
class IsFeatureEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): mixed  $next
     * @param  string  $flag
     * @param  string  $message
     * @return mixed
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function handle(Request $request, Closure $next, string $flag, string $message): mixed
    {
        /** @var User|null $user */
        $user = $request->user('sanctum');

        if (! Config::bool($flag) && empty($user?->can(SpecialPermission::BYPASS_FEATURE_FLAGS))) {
            abort(403, $message);
        }

        return $next($request);
    }
}
