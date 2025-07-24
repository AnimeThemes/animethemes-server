<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface ManagesTrackEvent
{
    /**
     * Manages a track in a playlist.
     */
    public function manageTrack(): void;
}
