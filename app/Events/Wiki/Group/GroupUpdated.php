<?php

declare(strict_types=1);

namespace App\Events\Wiki\Group;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Group;

/**
 * @extends WikiUpdatedEvent<Group>
 */
class GroupUpdated extends WikiUpdatedEvent
{
    public function __construct(Group $group)
    {
        parent::__construct($group);
        $this->initializeEmbedFields($group);
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Group '**{$this->getModel()->getName()}**' has been updated.";
    }
}
