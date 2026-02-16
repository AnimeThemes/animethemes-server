<?php

declare(strict_types=1);

namespace App\Listeners\List;

use App\Contracts\Events\UpdatePlaylistTracksEvent;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class UpdatePlaylistTracks implements ShouldHandleEventsAfterCommit
{
    public function handle(UpdatePlaylistTracksEvent $event): void
    {
        $event->updatePlaylistTracks();
    }
}
