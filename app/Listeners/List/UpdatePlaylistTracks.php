<?php

declare(strict_types=1);

namespace App\Listeners\List;

use App\Contracts\Events\UpdatePlaylistTracksEvent;

class UpdatePlaylistTracks
{
    public function handle(UpdatePlaylistTracksEvent $event): void
    {
        $event->updatePlaylistTracks();
    }
}
