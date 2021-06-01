<?php

declare(strict_types=1);

namespace App\Events\Theme;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Models\Entry;
use App\Models\Video;

/**
 * Class ThemeDeleting
 * @package App\Events\Theme
 */
class ThemeDeleting extends ThemeEvent implements CascadesDeletesEvent
{
    /**
     * Perform cascading deletes.
     *
     * @return void
     */
    public function cascadeDeletes()
    {
        $theme = $this->getTheme()->load('entries.videos');

        $theme->entries->each(function (Entry $entry) {
            Entry::withoutEvents(function () use ($entry) {
                $entry->unsearchable();
                $entry->delete();

                $videos = $entry->videos;
                $videos->each(function (Video $video) {
                    $video->searchable();
                });
            });
        });
    }
}
