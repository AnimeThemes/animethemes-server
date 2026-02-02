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
    protected function getDiscordMessageDescription(): string
    {
        return "Group '**{$this->getModel()->getName()}**' has been deleted.";
    }

    protected function getNotificationMessage(): string
    {
        return "Group '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return GroupFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}
