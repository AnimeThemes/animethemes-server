<?php

declare(strict_types=1);

namespace App\Events\Wiki\Theme;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;

/**
 * @extends BaseEvent<Theme>
 */
class ThemeDeleting extends BaseEvent implements CascadesDeletesEvent
{
    public function cascadeDeletes(): void
    {
        $theme = $this->getModel()->load(Theme::RELATION_VIDEOS);

        $theme->animethemeentries->each(function (Entry $entry): void {
            Entry::withoutEvents(function () use ($entry): void {
                $entry->unsearchable();
                $entry->delete();

                $videos = $entry->videos;
                $videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
