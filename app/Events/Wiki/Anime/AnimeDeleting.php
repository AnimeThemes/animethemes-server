<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Events\Wiki\Theme\ThemeDeleting;
use App\Models\Wiki\Synonym;
use App\Models\Wiki\Theme;
use Illuminate\Support\Facades\Event;

/**
 * Class AnimeDeleting.
 */
class AnimeDeleting extends AnimeEvent implements CascadesDeletesEvent
{
    /**
     * Perform cascading deletes.
     *
     * @return void
     */
    public function cascadeDeletes()
    {
        $anime = $this->getAnime()->load(['synonyms', 'themes.entries.videos']);

        $anime->synonyms->each(function (Synonym $synonym) {
            Synonym::withoutEvents(function () use ($synonym) {
                $synonym->unsearchable();
                $synonym->delete();
            });
        });

        $anime->themes->each(function (Theme $theme) {
            Theme::withoutEvents(function () use ($theme) {
                Event::until(new ThemeDeleting($theme));
                $theme->unsearchable();
                $theme->delete();
            });
        });
    }
}
