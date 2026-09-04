<?php

declare(strict_types=1);

namespace App\Events\Wiki\Entry;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Video;

/**
 * @extends WikiCreatedEvent<Entry>
 */
class EntryCreated extends WikiCreatedEvent implements UpdateRelatedIndicesEvent
{
    public function updateRelatedIndices(): void
    {
        $entry = $this->getModel();

        $entry->videos->each(fn (Video $video) => $video->searchable());
    }
}
