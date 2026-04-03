<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Performance;

use App\Contracts\Events\CreateSynonymEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Enums\Models\Wiki\SynonymType;
use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Performance;
use App\Models\Wiki\Synonym;

/**
 * @extends WikiCreatedEvent<Performance>
 */
class PerformanceCreated extends WikiCreatedEvent implements CreateSynonymEvent, UpdateRelatedIndicesEvent
{
    protected function getDiscordMessageDescription(): string
    {
        $performance = $this->getModel();

        $song = $performance->song;
        $artist = $performance->artist;

        $artistName = $performance->alias ?? $artist->getName();
        $artistName = filled($performance->as) ? "{$performance->as} (CV: {$artistName})" : $artistName;

        if ($this->getModel()->member instanceof Artist) {
            $groupName = $artistName;
            $member = $performance->member;

            $memberName = $performance->member_alias ?? $member->getName();
            $memberName = filled($performance->member_as) ? "{$performance->member_as} (CV: {$memberName})" : $memberName;

            return "Song '**{$song->getName()}**' has been attached to Member '**{$memberName}**' of '**{$groupName}**'.";
        }

        return "Song '**{$song->getName()}**' has been attached to Artist '**{$artistName}**'.";
    }

    public function updateRelatedIndices(): void
    {
        $performance = $this->getModel()->load([
            Performance::RELATION_ARTIST,
            Performance::RELATION_MEMBER,
        ]);

        $performance->artist->searchable();
        $performance->member?->searchable();
    }

    public function createSynonym(): void
    {
        $performance = $this->getModel();

        if ($performance->artist instanceof Artist && filled($performance->alias)) {
            $performance->artist->synonyms()->firstOrCreate([
                Synonym::ATTRIBUTE_TEXT => $performance->alias,
            ], [
                Synonym::ATTRIBUTE_TYPE => SynonymType::OTHER->value,
            ]);
        }
    }
}
