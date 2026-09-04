<?php

declare(strict_types=1);

namespace App\Events\Wiki\Entry;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\EntryResource as EntryFilament;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Video;

/**
 * @extends WikiDeletedEvent<Entry>
 */
class EntryDeleted extends WikiDeletedEvent implements UpdateRelatedIndicesEvent
{
    protected function getFilamentNotificationUrl(): string
    {
        return EntryFilament::getUrl('view', ['record' => $this->getModel()]);
    }

    public function updateRelatedIndices(): void
    {
        $entry = $this->getModel();

        $videos = $entry->videos;
        $videos->each(fn (Video $video) => $video->searchable());
    }
}
