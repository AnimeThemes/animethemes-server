<?php

declare(strict_types=1);

namespace App\Listeners\List;

use App\Contracts\Events\UpdatePlaylistTracksEvent;

/**
 * Class UpdatePlaylistTracks.
 */
class UpdatePlaylistTracks
{
    /**
     * Handle the event.
     *
     * @param  UpdatePlaylistTracksEvent  $event
     * @return void
     */
    public function handle(UpdatePlaylistTracksEvent $event): void
    {
        $event->updatePlaylistTracks();
    }
}
