<?php

declare(strict_types=1);

namespace App\Http\Middleware\Models\List;

use App\Constants\Config\PlaylistConstants;
use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class UserExceedsPlaylistLimit
{
    /**
     * @param  Closure(Request): mixed  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $playlistLimit = intval(Config::get(PlaylistConstants::MAX_PLAYLISTS_QUALIFIED));

        /** @var User|null $user */
        $user = $request->user('sanctum');

        abort_if(intval($user?->playlists()?->count()) >= $playlistLimit
        && empty($user?->can(SpecialPermission::BYPASS_FEATURE_FLAGS->value)), 403, "User cannot have more than '$playlistLimit' playlists.");

        return $next($request);
    }
}
