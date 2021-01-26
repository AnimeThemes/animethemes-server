<?php

namespace App\Events\Pivot\AnimeResource;

use App\Pivots\AnimeResource;

abstract class AnimeResourceEvent
{
    /**
     * The anime that this anime resource belongs to.
     *
     * @var \App\Models\Anime
     */
    protected $anime;

    /**
     * The resource that this anime resource belongs to.
     *
     * @var \App\Models\ExternalResource
     */
    protected $resource;

    /**
     * Create a new event instance.
     *
     * @param @var \App\Pivots\AnimeResource $animeResource
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
     * @return \App\Models\Anime
     */
    public function getAnime()
    {
        return $this->anime;
    }

    /**
     * Get the resource that this anime resource belongs to.
     *
     * @return \App\Models\ExternalResource
     */
    public function getResource()
    {
        return $this->resource;
    }
}
