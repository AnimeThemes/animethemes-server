<?php

declare(strict_types=1);

namespace App\Events\Wiki\Entry;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Video;

/**
 * @extends WikiRestoredEvent<Entry>
 */
class EntryRestored extends WikiRestoredEvent implements UpdateRelatedIndicesEvent
{
    public function updateRelatedIndices(): void
    {
        $entry = $this->getModel();

        $entry->videos->each(fn (Video $video) => $video->searchable());
    }
}
