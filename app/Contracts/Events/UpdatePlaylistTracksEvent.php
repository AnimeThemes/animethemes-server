<?php

declare(strict_types=1);

namespace App\Contracts\Events;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

interface UpdatePlaylistTracksEvent extends ShouldHandleEventsAfterCommit
{
    public function updatePlaylistTracks(): void;
}
