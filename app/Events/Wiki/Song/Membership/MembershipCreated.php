<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Membership;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Song\Membership;

/**
 * @extends WikiCreatedEvent<Membership>
 */
class MembershipCreated extends WikiCreatedEvent implements UpdateRelatedIndicesEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Membership '**{$this->getModel()->member->getName()}**' of Group '**{$this->getModel()->group->getName()}**' has been created.";
    }

    public function updateRelatedIndices(): void
    {
        $membership = $this->getModel()->load([Membership::RELATION_GROUP, Membership::RELATION_MEMBER]);

        $membership->group->searchable();
        $membership->member->searchable();
    }
}
