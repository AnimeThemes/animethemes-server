<?php

declare(strict_types=1);

namespace App\Events\Wiki\Entry;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Video;

/**
 * @extends BaseEvent<Entry>
 */
class EntryForceDeleting extends BaseEvent implements UpdateRelatedIndicesEvent
{
    public function updateRelatedIndices(): void
    {
        $entry = $this->getModel();

        // refresh video documents by detaching entry
        $videos = $entry->videos;
        $entry->videos()->detach();
        $videos->each(fn (Video $video) => $video->searchable());
    }
}
