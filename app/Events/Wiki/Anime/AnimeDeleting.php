<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Events\BaseEvent;
use App\Events\Wiki\Theme\ThemeDeleting;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Synonym;
use App\Models\Wiki\Theme;
use Illuminate\Support\Facades\Event;

/**
 * @extends BaseEvent<Anime>
 */
class AnimeDeleting extends BaseEvent implements CascadesDeletesEvent
{
    public function cascadeDeletes(): void
    {
        $anime = $this->getModel()->load([
            Anime::RELATION_SYNONYMS,
            Anime::RELATION_VIDEOS,
        ]);

        $anime->synonyms->each(function (Synonym $synonym): void {
            Synonym::withoutEvents(function () use ($synonym): void {
                $synonym->delete();
            });
        });

        $anime->animethemes->each(function (Theme $theme): void {
            Theme::withoutEvents(function () use ($theme): void {
                Event::until(new ThemeDeleting($theme));
                $theme->unsearchable();
                $theme->delete();
            });
        });
    }
}
