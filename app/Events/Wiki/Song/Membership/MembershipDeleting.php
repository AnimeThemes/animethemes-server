<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Membership;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Song\Membership;

/**
 * Class MembershipDeleting.
 *
 * @extends BaseEvent<Membership>
 */
class MembershipDeleting extends BaseEvent implements UpdateRelatedIndicesEvent
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
     * Perform cascading deletes.
     */
    public function updateRelatedIndices(): void
    {
        $membership = $this->getModel()->load([Membership::RELATION_ARTIST, Membership::RELATION_MEMBER]);

        if ($membership->isForceDeleting()) {
            $membership->artist->searchable();
            $membership->member->searchable();
        }
    }
}
