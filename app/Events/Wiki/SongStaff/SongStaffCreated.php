<?php

declare(strict_types=1);

namespace App\Events\Wiki\SongStaff;

use App\Contracts\Events\CreateSynonymEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Artist;
use App\Models\Wiki\SongStaff;
use App\Models\Wiki\Synonym;

/**
 * @extends WikiCreatedEvent<SongStaff>
 */
class SongStaffCreated extends WikiCreatedEvent implements CreateSynonymEvent, UpdateRelatedIndicesEvent
{
    protected function getDiscordMessageDescription(): string
    {
        $staff = $this->getModel();

        $song = $staff->song;
        $artist = $staff->artist;

        $artistName = $staff->alias ?? $artist->getName();
        $artistName = filled($staff->as) ? "{$staff->as} (CV: {$artistName})" : $artistName;

        if ($this->getModel()->member instanceof Artist) {
            $groupName = $artistName;
            $member = $staff->member;

            $memberName = $staff->member_alias ?? $member->getName();
            $memberName = filled($staff->member_as) ? "{$staff->member_as} (CV: {$memberName})" : $memberName;

            return "Song '**{$song->getName()}**' has been attached to Member '**{$memberName}**' of '**{$groupName}**' as '**{$staff->role}**'.";
        }

        return "Song '**{$song->getName()}**' has been attached to Artist '**{$artistName}**' as '**{$staff->role}**'.";
    }

    public function updateRelatedIndices(): void
    {
        $staff = $this->getModel()->load([
            SongStaff::RELATION_ARTIST,
            SongStaff::RELATION_MEMBER,
        ]);

        $staff->artist->searchable();
        $staff->member?->searchable();
    }

    public function createSynonym(): void
    {
        $staff = $this->getModel();

        if ($staff->artist instanceof Artist && filled($staff->alias)) {
            $staff->artist->synonyms()->firstOrCreate([
                Synonym::ATTRIBUTE_TEXT => $staff->alias,
            ]);
        }
    }
}
