<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

/**
 * @extends WikiCreatedEvent<AnimeTheme>
 */
class ThemeCreated extends WikiCreatedEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(AnimeTheme $theme)
    {
        parent::__construct($theme);
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Theme '**{$this->getModel()->getName()}**' has been created for Anime '**{$this->getModel()->anime->getName()}**'.";
    }

    public function updateRelatedIndices(): void
    {
        $theme = $this->getModel()->load(AnimeTheme::RELATION_VIDEOS);

        $theme->animethemeentries->each(function (AnimeThemeEntry $entry): void {
            $entry->searchable();
            $entry->videos->each(fn (Video $video) => $video->searchable());
        });
    }
}
