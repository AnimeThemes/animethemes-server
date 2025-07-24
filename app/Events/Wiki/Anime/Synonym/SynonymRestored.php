<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Synonym;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

/**
 * Class SynonymRestored.
 *
 * @extends WikiRestoredEvent<AnimeSynonym>
 */
class SynonymRestored extends WikiRestoredEvent implements UpdateRelatedIndicesEvent
{
    /**
     * The anime that the synonym belongs to.
     */
    protected Anime $anime;

    public function __construct(AnimeSynonym $synonym)
    {
        parent::__construct($synonym);
        $this->anime = $synonym->anime;
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): AnimeSynonym
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Synonym '**{$this->getModel()->getName()}**' has been restored for Anime '**{$this->anime->getName()}**'.";
    }

    /**
     * Perform updates on related indices.
     */
    public function updateRelatedIndices(): void
    {
        $synonym = $this->getModel()->load(AnimeSynonym::RELATION_VIDEOS);

        $synonym->anime->searchable();
        $synonym->anime->animethemes->each(function (AnimeTheme $theme) {
            $theme->searchable();
            $theme->animethemeentries->each(function (AnimeThemeEntry $entry) {
                $entry->searchable();
                $entry->videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
