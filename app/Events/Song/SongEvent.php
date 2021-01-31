<?php

namespace App\Events\Song;

use App\Models\Song;

abstract class SongEvent
{
    /**
     * The song that has fired this event.
     *
     * @var \App\Models\Song
     */
    protected $song;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Song $song
     * @return void
     */
    public function __construct(Song $song)
    {
        $this->song = $song;
    }

    /**
     * Get the song that has fired this event.
     *
     * @return \App\Models\Song
     */
    public function getSong()
    {
        return $this->song;
    }
}
