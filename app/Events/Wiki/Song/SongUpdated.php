<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Song;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;

/**
 * @extends WikiUpdatedEvent<Song>
 */
class SongUpdated extends WikiUpdatedEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(Song $song)
    {
        parent::__construct($song);
        $this->initializeEmbedFields($song);
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
