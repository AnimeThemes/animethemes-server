<?php

declare(strict_types=1);

namespace App\Http\Middleware\Models\List;

use App\Constants\Config\ExternalProfileConstants;
use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class UserExceedsExternalProfileLimit
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
        $profileLimit = intval(Config::get(ExternalProfileConstants::MAX_PROFILES_QUALIFIED));

        /** @var User|null $user */
        $user = $request->user('sanctum');

        if (
            intval($user?->externalprofiles()?->count()) >= $profileLimit
            && empty($user?->can(SpecialPermission::BYPASS_FEATURE_FLAGS->value))
        ) {
            abort(403, "User cannot have more than '$profileLimit' external profiles.");
        }

        return $next($request);
    }
}
