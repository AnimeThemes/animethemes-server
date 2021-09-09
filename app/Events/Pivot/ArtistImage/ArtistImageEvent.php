<?php

declare(strict_types=1);

namespace App\Events\Pivot\ArtistImage;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\ArtistImage;

/**
 * Class ArtistImageEvent.
 */
abstract class ArtistImageEvent
{
    /**
     * The artist that this artist image belongs to.
     *
     * @var Artist
     */
    protected Artist $artist;

    /**
     * The image that this artist image belongs to.
     *
     * @var Image
     */
    protected Image $image;

    /**
     * Create a new event instance.
     *
     * @param  ArtistImage  $artistImage
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
     * @return Artist
     */
    public function getArtist(): Artist
    {
        return $this->artist;
    }

    /**
     * Get the image that this artist image belongs to.
     *
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }
}
