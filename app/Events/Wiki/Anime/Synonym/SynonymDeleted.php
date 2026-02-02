<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Synonym;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Anime\SynonymResource as SynonymFilament;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Series;
use App\Models\Wiki\Video;

/**
 * @extends WikiDeletedEvent<AnimeSynonym>
 */
class SynonymDeleted extends WikiDeletedEvent implements UpdateRelatedIndicesEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Synonym '**{$this->getModel()->getName()}**' has been deleted for Anime '**{$this->getModel()->anime->getName()}**'.";
    }

    protected function getNotificationMessage(): string
    {
        return "Synonym '{$this->getModel()->getName()}' has been deleted for Anime '{$this->getModel()->anime->getName()}'. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return SynonymFilament::getUrl('view', ['record' => $this->getModel()]);
    }

    public function updateRelatedIndices(): void
    {
        $anime = $this->getModel()->anime->load([
            Anime::RELATION_SERIES,
            Anime::RELATION_VIDEOS,
        ]);

        $anime->searchable();
        $anime->series->each(fn (Series $series) => $series->searchable());
        $anime->animethemes->each(function (AnimeTheme $theme): void {
            $theme->searchable();
            $theme->animethemeentries->each(function (AnimeThemeEntry $entry): void {
                $entry->searchable();
                $entry->videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
