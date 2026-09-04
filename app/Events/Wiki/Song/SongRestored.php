<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Song;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;

/**
 * @extends WikiRestoredEvent<Song>
 */
class SongRestored extends WikiRestoredEvent implements UpdateRelatedIndicesEvent
{
    public function updateRelatedIndices(): void
    {
        $song = $this->getModel()->load([Song::RELATION_VIDEOS]);

        $song->animethemes->each(function (Theme $theme): void {
            $theme->searchable();
            $theme->animethemeentries->each(function (Entry $entry): void {
                $entry->searchable();
                $entry->videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
