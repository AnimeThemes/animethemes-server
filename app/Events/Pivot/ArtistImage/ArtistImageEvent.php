<?php

namespace App\Events\Pivot\ArtistImage;

use App\Pivots\ArtistImage;

abstract class ArtistImageEvent
{
    /**
     * The artist that this artist image belongs to.
     *
     * @var \App\Models\Artist
     */
    protected $artist;

    /**
     * The image that this artist image belongs to.
     *
     * @var \App\Models\Image
     */
    protected $image;

    /**
     * Create a new event instance.
     *
     * @param @var \App\Pivots\ArtistImage $artistImage
     * @return void
     */
    public function __construct(ArtistImage $artistImage)
    {
        $this->artist = $artistImage->artist;
        $this->image = $artistImage->image;
    }

    /**
     * Get the artist that this artist image belongs to.
     *
     * @return \App\Models\Artist
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * Get the image that this artist image belongs to.
     *
     * @return \App\Models\Image
     */
    public function getImage()
    {
        return $this->image;
    }
}
