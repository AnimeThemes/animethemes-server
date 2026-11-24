<?php

declare(strict_types=1);

namespace App\Events\Wiki\Entry;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\EntryResource as EntryFilament;
use App\Models\Wiki\Entry;

/**
 * @extends WikiDeletedEvent<Entry>
 */
class EntryDeleted extends WikiDeletedEvent
{
    protected function getFilamentNotificationUrl(): string
    {
        return EntryFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}
