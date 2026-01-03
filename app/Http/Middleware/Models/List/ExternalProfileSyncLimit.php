<?php

declare(strict_types=1);

namespace App\Http\Middleware\Models\List;

use App\Enums\Auth\Role;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExternalProfileSyncLimit
{
    /**
     * @param  Closure(Request): mixed  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var ExternalProfile $profile */
        $profile = $request->route('externalprofile');

        /** @var User|null $user */
        $user = Auth::user();

        abort_unless(
            $profile->canBeSynced() || $user?->hasRole(Role::ADMIN->value),
            403,
            'This external profile cannot be synced at the moment.'
        );

        return $next($request);
    }
}
