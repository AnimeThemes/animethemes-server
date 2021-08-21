<?php

declare(strict_types=1);

namespace App\Events\Pivot\AnimeStudio;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\AnimeStudio;

/**
 * Class AnimeStudioEvent.
 */
abstract class AnimeStudioEvent
{
    /**
     * The anime that this anime studio belongs to.
     *
     * @var Anime
     */
    protected Anime $anime;

    /**
     * The studio that this anime studio belongs to.
     *
     * @var Studio
     */
    protected Studio $studio;

    /**
     * Create a new event instance.
     *
     * @param AnimeStudio $animeStudio
     * @return void
     */
    public function __construct(AnimeStudio $animeStudio)
    {
        $this->anime = $animeStudio->anime;
        $this->studio = $animeStudio->studio;
    }

    /**
     * Get the anime that this anime studio belongs to.
     *
     * @return Anime
     */
    public function getAnime(): Anime
    {
        return $this->anime;
    }

    /**
     * Get the studio that this anime studio belongs to.
     *
     * @return Studio
     */
    public function getStudio(): Studio
    {
        return $this->studio;
    }
}
