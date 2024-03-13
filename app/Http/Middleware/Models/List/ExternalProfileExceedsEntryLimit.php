<?php

declare(strict_types=1);

namespace App\Http\Middleware\Models\List;

use App\Constants\Config\ExternalProfileConstants;
use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Class ExternalProfileExceedsEntryLimit.
 */
class ExternalProfileExceedsEntryLimit
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
        $entryLimit = intval(Config::get(ExternalProfileConstants::MAX_ENTRIES_QUALIFIED));

        /** @var ExternalProfile|null $profile */
        $profile = $request->route('externalprofile');

        /** @var User|null $user */
        $user = $request->user('sanctum');

        if (
            intval($profile?->externalentries()?->count()) >= $entryLimit
            && empty($user?->can(SpecialPermission::BYPASS_FEATURE_FLAGS->value))
        ) {
            abort(403, "External profiles cannot contain more than '$entryLimit' entries.");
        }

        return $next($request);
    }
}
