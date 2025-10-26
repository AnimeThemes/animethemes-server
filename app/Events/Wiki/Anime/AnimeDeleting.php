<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Events\BaseEvent;
use App\Events\Wiki\Anime\Theme\ThemeDeleting;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Support\Facades\Event;

/**
 * @extends BaseEvent<Anime>
 */
class AnimeDeleting extends BaseEvent implements CascadesDeletesEvent
{
    public function cascadeDeletes(): void
    {
        $anime = $this->getModel()->load([Anime::RELATION_SYNONYMS, Anime::RELATION_VIDEOS]);

        $anime->animesynonyms->each(function (AnimeSynonym $synonym): void {
            AnimeSynonym::withoutEvents(function () use ($synonym): void {
                $synonym->unsearchable();
                $synonym->delete();
            });
        });

        $anime->animethemes->each(function (AnimeTheme $theme): void {
            AnimeTheme::withoutEvents(function () use ($theme): void {
                Event::until(new ThemeDeleting($theme));
                $theme->unsearchable();
                $theme->delete();
            });
        });
    }
}
