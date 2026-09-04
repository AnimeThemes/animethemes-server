<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Song;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;

/**
 * @extends BaseEvent<Song>
 */
class SongForceDeleting extends BaseEvent implements UpdateRelatedIndicesEvent
{
    public function updateRelatedIndices(): void
    {
        $song = $this->getModel()->load([Song::RELATION_VIDEOS]);

        // refresh theme documents by dissociating song
        $song->animethemes->each(function (Theme $theme): void {
            Theme::withoutEvents(function () use ($theme): void {
                $theme->song()->dissociate();
                $theme->save();
            });
            $theme->searchable();
            $theme->animethemeentries->each(function (Entry $entry): void {
                $entry->searchable();
                $entry->videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
