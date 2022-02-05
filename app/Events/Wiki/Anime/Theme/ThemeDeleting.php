<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

/**
 * Class ThemeDeleting.
 */
class ThemeDeleting extends ThemeEvent implements CascadesDeletesEvent
{
    /**
     * Perform cascading deletes.
     *
     * @return void
     */
    public function cascadeDeletes(): void
    {
        $theme = $this->getTheme()->load(AnimeTheme::RELATION_VIDEOS);

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
