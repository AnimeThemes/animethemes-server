<?php

declare(strict_types=1);

namespace App\Contracts\Events;

/**
 * Interface SyncArtistSongEvent.
 */
interface SyncArtistSongEvent
{
    /**
     * Sync the performance with the artist song.
     * Temporary function.
     *
     * @return void
     */
    public function syncArtistSong(): void;
}
