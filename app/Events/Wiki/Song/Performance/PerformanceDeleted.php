<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song\Performance;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Song\PerformanceResource as PerformanceFilament;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Performance;

/**
 * @extends WikiDeletedEvent<Performance>
 */
class PerformanceDeleted extends WikiDeletedEvent implements UpdateRelatedIndicesEvent
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

            return "Song '**{$song->getName()}**' has been detached from Member '**{$memberName}**' of '**{$groupName}**'.";
        }

        return "Song '**{$song->getName()}**' has been detached from Artist '**{$artistName}**'.";
    }

    protected function getNotificationMessage(): string
    {
        return "Performance '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return PerformanceFilament::getUrl('view', ['record' => $this->getModel()]);
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
}
