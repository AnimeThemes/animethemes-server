<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface UpdatePlaylistTracksEvent
{
    /**
     * Update the related playlist tracks.
     */
    public function updatePlaylistTracks(): void;
}
