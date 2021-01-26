<?php

namespace App\Events\Theme;

use App\Models\Theme;

abstract class ThemeEvent
{
    /**
     * The theme that has fired this event.
     *
     * @var \App\Models\Theme
     */
    protected $theme;

    /**
     * The anime that the theme belongs to.
     *
     * @var \App\Models\Anime
     */
    protected $anime;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Theme $theme
     * @return void
     */
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
        $this->anime = $theme->anime;
    }

    /**
     * Get the theme that has fired this event.
     *
     * @return \App\Models\Theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Get the anime that the theme belongs to.
     *
     * @return \App\Models\Anime
     */
    public function getAnime()
    {
        return $this->anime;
    }
}
