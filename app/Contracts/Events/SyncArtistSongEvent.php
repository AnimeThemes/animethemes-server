<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface SyncArtistSongEvent
{
    /**
     * Sync the performance with the artist song.
     * Temporary function.
     */
    public function syncArtistSong(): void;
}
