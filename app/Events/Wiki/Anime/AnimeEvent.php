<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime;

use App\Models\Wiki\Anime;

/**
 * Class AnimeEvent.
 */
abstract class AnimeEvent
{
    /**
     * The anime that has fired this event.
     *
     * @var Anime
     */
    protected Anime $anime;

    /**
     * Create a new event instance.
     *
     * @param Anime $anime
     * @return void
     */
    public function __construct(Anime $anime)
    {
        $this->anime = $anime;
    }

    /**
     * Get the anime that has fired this event.
     *
     * @return Anime
     */
    public function getAnime(): Anime
    {
        return $this->anime;
    }
}
