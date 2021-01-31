<?php

namespace App\Events\Pivot\AnimeImage;

use App\Pivots\AnimeImage;

abstract class AnimeImageEvent
{
    /**
     * The anime that this anime image belongs to.
     *
     * @var \App\Models\Anime
     */
    protected $anime;

    /**
     * The image that this anime image belongs to.
     *
     * @var \App\Models\Image
     */
    protected $image;

    /**
     * Create a new event instance.
     *
     * @param \App\Pivots\AnimeImage $animeImage
     * @return void
     */
    public function __construct(AnimeImage $animeImage)
    {
        $this->anime = $animeImage->anime;
        $this->image = $animeImage->image;
    }

    /**
     * Get the anime that this anime image belongs to.
     *
     * @return \App\Models\Anime
     */
    public function getAnime()
    {
        return $this->anime;
    }

    /**
     * Get the image that this anime image belongs to.
     *
     * @return \App\Models\Image
     */
    public function getImage()
    {
        return $this->image;
    }
}
