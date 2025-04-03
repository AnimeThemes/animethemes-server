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
    /**
     * Create a new event instance.
     *
     * @param  Membership  $membership
     */
    public function __construct(Membership $membership)
    {
        parent::__construct($membership);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Membership
     */
    public function getModel(): Membership
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Membership '**{$this->getModel()->getName()}**' has been restored.";
    }

    /**
     * Perform cascading deletes.
     *
     * @return void
     */
    public function updateRelatedIndices(): void
    {
        $membership = $this->getModel()->load([Membership::RELATION_ARTIST, Membership::RELATION_MEMBER]);

        $membership->artist->searchable();
        $membership->member->searchable();
    }
}
