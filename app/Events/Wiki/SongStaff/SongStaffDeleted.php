<?php

declare(strict_types=1);

namespace App\Events\Wiki\SongStaff;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\SongStaffResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\SongStaff;

/**
 * @extends WikiDeletedEvent<SongStaff>
 */
class SongStaffDeleted extends WikiDeletedEvent implements UpdateRelatedIndicesEvent
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

            return "Song '**{$song->getName()}**' has been detached from Member '**{$memberName}**' of '**{$groupName}**' as **{$staff->role}**.";
        }

        return "Song '**{$song->getName()}**' has been detached from Artist '**{$artistName}**' as **{$staff->role}**.";
    }

    protected function getNotificationMessage(): string
    {
        return "Song Staff '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return SongStaffResource::getUrl('view', ['record' => $this->getModel()]);
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
}
