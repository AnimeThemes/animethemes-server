<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface UpdatePlaylistTracksEvent
{
    public function updatePlaylistTracks(): void;
}
