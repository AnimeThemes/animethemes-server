<?php

declare(strict_types=1);

namespace App\Events\Wiki\Entry;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Video;

/**
 * @extends WikiUpdatedEvent<Entry>
 */
class EntryUpdated extends WikiUpdatedEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(Entry $entry)
    {
        parent::__construct($entry);
        $this->initializeEmbedFields($entry);
    }

    public function updateRelatedIndices(): void
    {
        $entry = $this->getModel();

        $entry->videos->each(fn (Video $video) => $video->searchable());
    }
}
