<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme;

use App\Events\BaseEvent;
use App\Models\Wiki\Anime\AnimeTheme;

/**
 * Class ThemeCreating.
 *
 * @extends BaseEvent<AnimeTheme>
 */
class ThemeCreating extends BaseEvent
{
    /**
     * Create a new event instance.
     *
     * @param  AnimeTheme  $theme
     */
    public function __construct(AnimeTheme $theme)
    {
        parent::__construct($theme);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return AnimeTheme
     */
    public function getModel(): AnimeTheme
    {
        return $this->model;
    }
}
