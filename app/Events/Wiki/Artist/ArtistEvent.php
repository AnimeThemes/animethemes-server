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
     * Create a new event instance.
     *
     * @param  Artist  $artist
     * @return void
     */
    public function __construct(protected Artist $artist)
    {
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
