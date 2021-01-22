<?php

namespace App\Events\Artist;

use App\Models\Artist;

abstract class ArtistEvent
{
    /**
     * The artist that has fired this event.
     *
     * @var \App\Models\Artist
     */
    protected $artist;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Artist $artist
     * @return void
     */
    public function __construct(Artist $artist)
    {
        $this->artist = $artist;
    }

    /**
     * Get the artist that has fired this event.
     *
     * @return \App\Models\Artist
     */
    public function getArtist()
    {
        return $this->artist;
    }
}
