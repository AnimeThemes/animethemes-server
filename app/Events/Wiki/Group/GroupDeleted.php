<?php

declare(strict_types=1);

namespace App\Events\Wiki\Group;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\GroupResource as GroupFilament;
use App\Models\Wiki\Group;

/**
 * @extends WikiDeletedEvent<Group>
 */
class GroupDeleted extends WikiDeletedEvent
{
    protected function getFilamentNotificationUrl(): string
    {
        return GroupFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}
