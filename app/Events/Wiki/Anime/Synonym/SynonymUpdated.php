<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Synonym;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

/**
 * @extends WikiUpdatedEvent<AnimeSynonym>
 */
class SynonymUpdated extends WikiUpdatedEvent implements UpdateRelatedIndicesEvent
{
    /**
     * The anime that the synonym belongs to.
     */
    protected Anime $anime;

    public function __construct(AnimeSynonym $synonym)
    {
        parent::__construct($synonym);
        $this->anime = $synonym->anime;
        $this->initializeEmbedFields($synonym);
    }

    public function getModel(): AnimeSynonym
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Synonym '**{$this->getModel()->getName()}**' has been updated for Anime '**{$this->anime->getName()}**'.";
    }

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
