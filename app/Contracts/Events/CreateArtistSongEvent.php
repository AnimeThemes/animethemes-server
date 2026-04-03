<?php

declare(strict_types=1);

namespace App\Contracts\Events;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

interface CreateArtistSongEvent extends ShouldHandleEventsAfterCommit
{
    public function createArtistSong(): void;
}
