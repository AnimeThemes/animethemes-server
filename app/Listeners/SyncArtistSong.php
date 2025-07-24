<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\SyncArtistSongEvent;

class SyncArtistSong
{
    /**
     * Handle the event.
     */
    public function handle(SyncArtistSongEvent $event): void
    {
        $event->syncArtistSong();
    }
}
