<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\SongResource as SongFilament;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Song;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;

/**
 * @extends WikiDeletedEvent<Song>
 */
class SongDeleted extends WikiDeletedEvent implements UpdateRelatedIndicesEvent
{
    protected function getFilamentNotificationUrl(): string
    {
        return SongFilament::getUrl('view', ['record' => $this->getModel()]);
    }

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
