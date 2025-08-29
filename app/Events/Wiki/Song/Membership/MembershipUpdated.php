<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Membership;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Song\Membership;

/**
 * @extends WikiUpdatedEvent<Membership>
 */
class MembershipUpdated extends WikiUpdatedEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(Membership $membership)
    {
        parent::__construct($membership);
        $this->initializeEmbedFields($membership);
    }

    public function getModel(): Membership
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Membership '**{$this->getModel()->getName()}**' has been updated.";
    }

    public function updateRelatedIndices(): void
    {
        $membership = $this->getModel()->load([Membership::RELATION_GROUP, Membership::RELATION_MEMBER]);

        $membership->group->searchable();
        $membership->member->searchable();
    }
}
