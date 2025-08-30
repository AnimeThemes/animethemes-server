<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

/**
 * @extends BaseEvent<AnimeTheme>
 */
class ThemeDeleting extends BaseEvent implements CascadesDeletesEvent
{
    public function __construct(AnimeTheme $theme)
    {
        parent::__construct($theme);
    }

    public function getModel(): AnimeTheme
    {
        return $this->model;
    }

    public function cascadeDeletes(): void
    {
        $theme = $this->getModel()->load(AnimeTheme::RELATION_VIDEOS);

        $theme->animethemeentries->each(function (AnimeThemeEntry $entry) {
            AnimeThemeEntry::withoutEvents(function () use ($entry) {
                $entry->unsearchable();
                $entry->delete();

                $videos = $entry->videos;
                $videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
