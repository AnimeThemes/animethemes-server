<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\SyncArtistSongEvent;

class SyncArtistSong
{
    public function handle(SyncArtistSongEvent $event): void
    {
        $event->syncArtistSong();
    }
}
