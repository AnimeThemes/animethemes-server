<?php

declare(strict_types=1);

namespace App\Events\Wiki\Synonym;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Series;
use App\Models\Wiki\Synonym;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @extends WikiRestoredEvent<Synonym>
 */
class SynonymRestored extends WikiRestoredEvent implements UpdateRelatedIndicesEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Synonym '**{$this->getModel()->getName()}**' has been restored for {$this->privateLabel($this->getModel()->synonymable)} '**{$this->getModel()->synonymable->getName()}**'.";
    }

    public function updateRelatedIndices(): void
    {
        $synonym = $this->getModel()->load([
            Synonym::RELATION_SYNONYMABLE => fn (MorphTo $morphTo): MorphTo => $morphTo->morphWith([
                Anime::class => [
                    Anime::RELATION_SERIES,
                    Anime::RELATION_VIDEOS,
                ],
            ]),
        ]);

        if ($synonym->synonymable instanceof Anime) {
            $synonym->synonymable->searchable();
            $synonym->synonymable->series->each(fn (Series $series) => $series->searchable());
            $synonym->synonymable->animethemes->each(function (AnimeTheme $theme): void {
                $theme->searchable();
                $theme->animethemeentries->each(function (AnimeThemeEntry $entry): void {
                    $entry->searchable();
                    $entry->videos->each(fn (Video $video) => $video->searchable());
                });
            });
        }
    }
}
