<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime;

use App\Contracts\Events\CascadesRestoresEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Synonym;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * @extends WikiRestoredEvent<Anime>
 */
class AnimeRestored extends WikiRestoredEvent implements CascadesRestoresEvent
{
    public function cascadeRestores(): void
    {
        $anime = $this->getModel();

        $anime->synonyms()->withoutGlobalScope(SoftDeletingScope::class)->get()->each(function (Synonym $synonym): void {
            Synonym::withoutEvents(function () use ($synonym): void {
                $synonym->restore();
            });
        });

        $anime->animethemes()->withoutGlobalScope(SoftDeletingScope::class)->get()->each(function (Theme $theme): void {
            Theme::withoutEvents(function () use ($theme): void {
                $theme->restore();
                $theme->searchable();
                $theme->animethemeentries()->withoutGlobalScope(SoftDeletingScope::class)->get()->each(function (Entry $entry): void {
                    Entry::withoutEvents(function () use ($entry): void {
                        $entry->restore();
                        $entry->searchable();
                        $entry->videos->each(fn (Video $video) => $video->searchable());
                    });
                });
            });
        });
    }
}
