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
     * Create a new event instance.
     *
     * @param  Anime  $anime
     * @return void
     */
    public function __construct(protected Anime $anime) {}

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
