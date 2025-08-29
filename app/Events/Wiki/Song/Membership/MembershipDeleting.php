<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Membership;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Song\Membership;

/**
 * @extends BaseEvent<Membership>
 */
class MembershipDeleting extends BaseEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(Membership $membership)
    {
        parent::__construct($membership);
    }

    public function getModel(): Membership
    {
        return $this->model;
    }

    public function updateRelatedIndices(): void
    {
        $membership = $this->getModel()->load([Membership::RELATION_GROUP, Membership::RELATION_MEMBER]);

        if ($membership->isForceDeleting()) {
            $membership->group->searchable();
            $membership->member->searchable();
        }
    }
}
