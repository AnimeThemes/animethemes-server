<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Events\Wiki\Anime\Theme\ThemeDeleting;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
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
        $anime = $this->getAnime()->load(['animesynonyms', 'animethemes.animethemeentries.videos']);

        $anime->animesynonyms->each(function (AnimeSynonym $synonym) {
            AnimeSynonym::withoutEvents(function () use ($synonym) {
                $synonym->unsearchable();
                $synonym->delete();
            });
        });

        $anime->animethemes->each(function (AnimeTheme $theme) {
            AnimeTheme::withoutEvents(function () use ($theme) {
                Event::until(new ThemeDeleting($theme));
                $theme->unsearchable();
                $theme->delete();
            });
        });
    }
}
