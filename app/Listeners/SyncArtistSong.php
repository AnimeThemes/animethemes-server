<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Events\SyncArtistSongEvent;

/**
 * Class SyncArtistSong.
 */
class SyncArtistSong
{
    /**
     * Handle the event.
     *
     * @param  SyncArtistSongEvent  $event
     * @return void
     */
    public function handle(SyncArtistSongEvent $event): void
    {
        $event->syncArtistSong();
    }
}
