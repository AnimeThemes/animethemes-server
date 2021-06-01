<?php declare(strict_types=1);

namespace App\Events\Pivot\AnimeImage;

use App\Models\Anime;
use App\Models\Image;
use App\Pivots\AnimeImage;

/**
 * Class AnimeImageEvent
 * @package App\Events\Pivot\AnimeImage
 */
abstract class AnimeImageEvent
{
    /**
     * The anime that this anime image belongs to.
     *
     * @var Anime
     */
    protected Anime $anime;

    /**
     * The image that this anime image belongs to.
     *
     * @var Image
     */
    protected Image $image;

    /**
     * Create a new event instance.
     *
     * @param AnimeImage $animeImage
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
     * @return Anime
     */
    public function getAnime(): Anime
    {
        return $this->anime;
    }

    /**
     * Get the image that this anime image belongs to.
     *
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }
}
