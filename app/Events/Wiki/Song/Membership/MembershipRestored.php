<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Membership;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Song\Membership;

/**
 * Class MembershipRestored.
 *
 * @extends WikiRestoredEvent<Membership>
 */
class MembershipRestored extends WikiRestoredEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(Membership $membership)
    {
        parent::__construct($membership);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Membership
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Membership '**{$this->getModel()->getName()}**' has been restored.";
    }

    /**
     * Perform cascading deletes.
     */
    public function updateRelatedIndices(): void
    {
        $membership = $this->getModel()->load([Membership::RELATION_GROUP, Membership::RELATION_MEMBER]);

        $membership->group->searchable();
        $membership->member->searchable();
    }
}
