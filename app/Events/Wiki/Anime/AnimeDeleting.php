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
    public function __construct(Anime $anime)
    {
        parent::__construct($anime);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Anime
    {
        return $this->model;
    }

    /**
     * Perform cascading deletes.
     */
    public function cascadeDeletes(): void
    {
        $anime = $this->getModel()->load([Anime::RELATION_SYNONYMS, Anime::RELATION_VIDEOS]);

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
