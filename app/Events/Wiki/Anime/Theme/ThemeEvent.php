<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;

/**
 * Class ThemeEvent.
 */
abstract class ThemeEvent
{
    /**
     * The theme that has fired this event.
     *
     * @var AnimeTheme
     */
    protected AnimeTheme $theme;

    /**
     * The anime that the theme belongs to.
     *
     * @var Anime
     */
    protected Anime $anime;

    /**
     * Create a new event instance.
     *
     * @param AnimeTheme $theme
     * @return void
     */
    public function __construct(AnimeTheme $theme)
    {
        $this->theme = $theme;
        $this->anime = $theme->anime;
    }

    /**
     * Get the theme that has fired this event.
     *
     * @return AnimeTheme
     */
    public function getTheme(): AnimeTheme
    {
        return $this->theme;
    }

    /**
     * Get the anime that the theme belongs to.
     *
     * @return Anime
     */
    public function getAnime(): Anime
    {
        return $this->anime;
    }
}
