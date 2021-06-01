<?php declare(strict_types=1);

namespace App\Events\Pivot\AnimeResource;

use App\Models\Anime;
use App\Models\ExternalResource;
use App\Pivots\AnimeResource;

/**
 * Class AnimeResourceEvent
 * @package App\Events\Pivot\AnimeResource
 */
abstract class AnimeResourceEvent
{
    /**
     * The anime that this anime resource belongs to.
     *
     * @var Anime
     */
    protected Anime $anime;

    /**
     * The resource that this anime resource belongs to.
     *
     * @var ExternalResource
     */
    protected ExternalResource $resource;

    /**
     * Create a new event instance.
     *
     * @param AnimeResource $animeResource
     * @return void
     */
    public function __construct(AnimeResource $animeResource)
    {
        $this->anime = $animeResource->anime;
        $this->resource = $animeResource->resource;
    }

    /**
     * Get the anime that this anime resource belongs to.
     *
     * @return Anime
     */
    public function getAnime(): Anime
    {
        return $this->anime;
    }

    /**
     * Get the resource that this anime resource belongs to.
     *
     * @return ExternalResource
     */
    public function getResource(): ExternalResource
    {
        return $this->resource;
    }
}
