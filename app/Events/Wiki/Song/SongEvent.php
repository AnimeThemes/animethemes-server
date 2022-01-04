<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song;

use App\Models\Wiki\Song;

/**
 * Class SongEvent.
 */
abstract class SongEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Song  $song
     * @return void
     */
    public function __construct(protected Song $song)
    {
    }

    /**
     * Get the song that has fired this event.
     *
     * @return Song
     */
    public function getSong(): Song
    {
        return $this->song;
    }
}
