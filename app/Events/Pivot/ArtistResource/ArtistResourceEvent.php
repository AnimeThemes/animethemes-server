<?php

declare(strict_types=1);

namespace App\Events\Pivot\ArtistResource;

use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\ArtistResource;

/**
 * Class ArtistResourceEvent.
 */
abstract class ArtistResourceEvent
{
    /**
     * The artist that this artist resource belongs to.
     *
     * @var Artist
     */
    protected Artist $artist;

    /**
     * The resource that this artist resource belongs to.
     *
     * @var ExternalResource
     */
    protected ExternalResource $resource;

    /**
     * Create a new event instance.
     *
     * @param ArtistResource $artistResource
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
     * @return Artist
     */
    public function getArtist(): Artist
    {
        return $this->artist;
    }

    /**
     * Get the resource that this artist resource belongs to.
     *
     * @return ExternalResource
     */
    public function getResource(): ExternalResource
    {
        return $this->resource;
    }
}
