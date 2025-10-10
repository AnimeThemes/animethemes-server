<?php

declare(strict_types=1);

namespace App\Http\Middleware\Models\List;

use App\Constants\Config\PlaylistConstants;
use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class PlaylistExceedsTrackLimit
{
    /**
     * @param  Closure(Request): mixed  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $trackLimit = intval(Config::get(PlaylistConstants::MAX_TRACKS_QUALIFIED));

        /** @var Playlist|null $playlist */
        $playlist = $request->route('playlist');

        /** @var User|null $user */
        $user = $request->user('sanctum');

        abort_if(intval($playlist?->tracks()?->count()) >= $trackLimit
        && blank($user?->can(SpecialPermission::BYPASS_FEATURE_FLAGS->value)), 403, "Playlists cannot contain more than '$trackLimit' tracks.");

        return $next($request);
    }
}
