<?php

declare(strict_types=1);

namespace App\Contracts\Events;

/**
 * Interface UpdatePlaylistTracksEvent.
 */
interface UpdatePlaylistTracksEvent
{
    /**
     * Update the related playlist tracks.
     *
     * @return void
     */
    public function updatePlaylistTracks(): void;
}
