<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Membership;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Song\Membership as MembershipFilament;
use App\Models\Wiki\Song\Membership;

/**
 * @extends WikiDeletedEvent<Membership>
 */
class MembershipDeleted extends WikiDeletedEvent implements UpdateRelatedIndicesEvent
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
        return "Membership '**{$this->getModel()->member->getName()}**' of Group '**{$this->getModel()->group->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the filament notification.
     */
    protected function getNotificationMessage(): string
    {
        return "Membership '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the Filament notification.
     */
    protected function getFilamentNotificationUrl(): string
    {
        return MembershipFilament::getUrl('view', ['record' => $this->getModel()]);
    }

    /**
     * Perform updates on related indices.
     */
    public function updateRelatedIndices(): void
    {
        $membership = $this->getModel()->load([Membership::RELATION_GROUP, Membership::RELATION_MEMBER]);

        $membership->group->searchable();
        $membership->member->searchable();
    }
}
