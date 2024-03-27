<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

/**
 * Class ThemeCreated.
 *
 * @extends WikiCreatedEvent<AnimeTheme>
 */
class ThemeCreated extends WikiCreatedEvent implements UpdateRelatedIndicesEvent
{
    /**
     * The anime that the theme belongs to.
     *
     * @var Anime
     */
    protected Anime $anime;

    /**
     * Create a new event instance.
     *
     * @param  AnimeTheme  $theme
     */
    public function __construct(AnimeTheme $theme)
    {
        parent::__construct($theme);
        $this->anime = $theme->anime;
        $this->updateFirstTheme();
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

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Theme '**{$this->getModel()->getName()}**' has been created for Anime '**{$this->anime->getName()}**'.";
    }

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices(): void
    {
        $theme = $this->getModel()->load(AnimeTheme::RELATION_VIDEOS);

        $theme->animethemeentries->each(function (AnimeThemeEntry $entry) {
            $entry->searchable();
            $entry->videos->each(fn (Video $video) => $video->searchable());
        });
    }

    /**
     * Update the sequence attribute of the first theme when creating a new sequence theme.
     * 
     * @return void
     */
    protected function updateFirstTheme(): void
    {
        if ($this->getModel()->sequence >= 2) {
            $this->anime->animethemes()->getQuery()
                ->where(AnimeTheme::ATTRIBUTE_SEQUENCE, null)
                ->where(AnimeTheme::ATTRIBUTE_TYPE, $this->getModel()->type)
                ->update([AnimeTheme::ATTRIBUTE_SEQUENCE => 1]);
        }
    }
}
