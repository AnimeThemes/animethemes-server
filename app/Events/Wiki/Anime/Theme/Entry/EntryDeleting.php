<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme\Entry;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

/**
 * @extends BaseEvent<AnimeThemeEntry>
 */
class EntryDeleting extends BaseEvent implements UpdateRelatedIndicesEvent
{
    public function updateRelatedIndices(): void
    {
        $entry = $this->getModel();

        if ($entry->isForceDeleting()) {
            // refresh video documents by detaching entry
            $videos = $entry->videos;
            $entry->videos()->detach();
            $videos->each(fn (Video $video) => $video->searchable());
        }
    }
}
