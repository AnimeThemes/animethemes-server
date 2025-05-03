<?php

declare(strict_types=1);

namespace App\Contracts\Events;

/**
 * Interface ManagesTrackEvent.
 */
interface ManagesTrackEvent
{
    /**
     * Manages a track in a playlist.
     *
     * @return void
     */
    public function manageTrack(): void;
}
