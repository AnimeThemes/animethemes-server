<?php

declare(strict_types=1);

namespace App\Concerns\Actions\List;

use App\Models\List\Playlist;
use Closure;
use Illuminate\Support\Facades\Cache;

trait LocksPlaylist
{
    protected function withPlaylistLock(Playlist $playlist, Closure $callback): mixed
    {
        return Cache::lock("playlist:{$playlist->getKey()}:lock", 30)
            ->block(10, fn () => $callback());
    }
}
