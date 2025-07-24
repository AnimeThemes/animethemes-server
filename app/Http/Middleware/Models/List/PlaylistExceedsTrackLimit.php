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
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): mixed  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $trackLimit = intval(Config::get(PlaylistConstants::MAX_TRACKS_QUALIFIED));

        /** @var Playlist|null $playlist */
        $playlist = $request->route('playlist');

        /** @var User|null $user */
        $user = $request->user('sanctum');

        if (
            intval($playlist?->tracks()?->count()) >= $trackLimit
            && empty($user?->can(SpecialPermission::BYPASS_FEATURE_FLAGS->value))
        ) {
            abort(403, "Playlists cannot contain more than '$trackLimit' tracks.");
        }

        return $next($request);
    }
}
