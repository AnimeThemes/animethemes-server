<?php

namespace App\Events\Pivot\ArtistResource;

use App\Pivots\ArtistResource;

abstract class ArtistResourceEvent
{
    /**
     * The artist that this artist resource belongs to.
     *
     * @var \App\Models\Artist
     */
    protected $artist;

    /**
     * The resource that this artist resource belongs to.
     *
     * @var \App\Models\ExternalResource
     */
    protected $resource;

    /**
     * Create a new event instance.
     *
     * @param \App\Pivots\ArtistResource $artistResource
     * @return void
     */
    public function __construct(ArtistResource $artistResource)
    {
        $this->artist = $artistResource->artist;
        $this->resource = $artistResource->resource;
    }

    /**
     * Get the artist that this artist resource belongs to.
     *
     * @return \App\Models\Artist
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * Get the resource that this artist resource belongs to.
     *
     * @return \App\Models\ExternalResource
     */
    public function getResource()
    {
        return $this->resource;
    }
}
