<?php

namespace App\Events\Anime;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Events\Theme\ThemeDeleting;
use App\Models\Synonym;
use App\Models\Theme;
use Illuminate\Support\Facades\Event;

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
