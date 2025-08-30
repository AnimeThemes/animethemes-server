<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

/**
 * @extends WikiUpdatedEvent<AnimeTheme>
 */
class ThemeUpdated extends WikiUpdatedEvent implements UpdateRelatedIndicesEvent
{
    /**
     * The anime that the theme belongs to.
     */
    protected Anime $anime;

    public function __construct(AnimeTheme $theme)
    {
        parent::__construct($theme);
        $this->anime = $theme->anime;
        $this->initializeEmbedFields($theme);
    }

    public function getModel(): AnimeTheme
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Theme '**{$this->getModel()->getName()}**' has been updated for Anime '**{$this->anime->getName()}**'.";
    }

    public function updateRelatedIndices(): void
    {
        $theme = $this->getModel()->load(AnimeTheme::RELATION_VIDEOS);

        $theme->animethemeentries->each(function (AnimeThemeEntry $entry) {
            $entry->searchable();
            $entry->videos->each(fn (Video $video) => $video->searchable());
        });
    }
}
