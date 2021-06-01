<?php

declare(strict_types=1);

namespace App\Events\Song;

use App\Models\Song;

/**
 * Class SongEvent
 * @package App\Events\Song
 */
abstract class SongEvent
{
    /**
     * The song that has fired this event.
     *
     * @var Song
     */
    protected Song $song;

    /**
     * Create a new event instance.
     *
     * @param Song $song
     * @return void
     */
    public function __construct(Song $song)
    {
        $this->song = $song;
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
