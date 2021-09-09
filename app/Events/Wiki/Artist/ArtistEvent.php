<?php

declare(strict_types=1);

namespace App\Events\Wiki\Artist;

use App\Models\Wiki\Artist;

/**
 * Class ArtistEvent.
 */
abstract class ArtistEvent
{
    /**
     * The artist that has fired this event.
     *
     * @var Artist
     */
    protected Artist $artist;

    /**
     * Create a new event instance.
     *
     * @param  Artist  $artist
     * @return void
     */
    public function __construct(Artist $artist)
    {
        $this->artist = $artist;
    }

    /**
     * Get the artist that has fired this event.
     *
     * @return Artist
     */
    public function getArtist(): Artist
    {
        return $this->artist;
    }
}
