<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Synonym;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Anime\Synonym as SynonymFilament;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

/**
 * @extends WikiDeletedEvent<AnimeSynonym>
 */
class SynonymDeleted extends WikiDeletedEvent implements UpdateRelatedIndicesEvent
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
        return "Synonym '**{$this->getModel()->getName()}**' has been deleted for Anime '**{$this->anime->getName()}**'.";
    }

    /**
     * Get the message for the filament notification.
     */
    protected function getNotificationMessage(): string
    {
        return "Synonym '{$this->getModel()->getName()}' has been deleted for Anime '{$this->anime->getName()}'. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the Filament notification.
     */
    protected function getFilamentNotificationUrl(): string
    {
        return SynonymFilament::getUrl('view', ['record' => $this->getModel()]);
    }

    /**
     * Perform updates on related indices.
     */
    public function updateRelatedIndices(): void
    {
        $anime = $this->anime->load(Anime::RELATION_VIDEOS);

        $anime->searchable();
        $anime->animethemes->each(function (AnimeTheme $theme) {
            $theme->searchable();
            $theme->animethemeentries->each(function (AnimeThemeEntry $entry) {
                $entry->searchable();
                $entry->videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
