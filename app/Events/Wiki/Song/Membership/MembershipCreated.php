<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Membership;

use App\Contracts\Events\CreateSynonymEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Enums\Models\Wiki\SynonymType;
use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Synonym;

/**
 * @extends WikiCreatedEvent<Membership>
 */
class MembershipCreated extends WikiCreatedEvent implements CreateSynonymEvent, UpdateRelatedIndicesEvent
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

    public function createSynonym(): void
    {
        $membership = $this->getModel();

        if (filled($membership->alias)) {
            $membership->member->synonyms()->firstOrCreate([
                Synonym::ATTRIBUTE_TEXT => $membership->alias,
            ], [
                Synonym::ATTRIBUTE_TYPE => SynonymType::OTHER->value,
            ]);
        }
    }
}
