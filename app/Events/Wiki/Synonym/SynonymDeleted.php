<?php

declare(strict_types=1);

namespace App\Events\Wiki\Synonym;

use App\Concerns\Models\HasLabel;
use App\Contracts\Events\UpdateAnimeSynonymsEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\SynonymResource as SynonymFilament;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Series;
use App\Models\Wiki\Synonym;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @extends WikiDeletedEvent<Synonym>
 */
class SynonymDeleted extends WikiDeletedEvent implements UpdateAnimeSynonymsEvent, UpdateRelatedIndicesEvent
{
    use HasLabel;

    protected function getDiscordMessageDescription(): string
    {
        return "Synonym '**{$this->getModel()->getName()}**' has been deleted for {$this->privateLabel($this->getModel()->synonymable)} '**{$this->getModel()->synonymable->getName()}**'.";
    }

    protected function getNotificationMessage(): string
    {
        return "Synonym '{$this->getModel()->getName()}' has been deleted for {$this->privateLabel($this->getModel()->synonymable)} '{$this->getModel()->synonymable->getName()}'. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return SynonymFilament::getUrl('view', ['record' => $this->getModel()]);
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

    public function updateAnimeSynonyms(): void
    {
        if ($this->getModel()->synonymable instanceof Anime) {
            AnimeSynonym::query()
                ->where(AnimeSynonym::ATTRIBUTE_ANIME, $this->getModel()->synonymable_id)
                ->where(AnimeSynonym::ATTRIBUTE_TEXT, $this->getModel()->text)
                ->{$this->getModel()->isForceDeleting() ? 'forceDelete' : 'delete'}();
        }
    }
}
