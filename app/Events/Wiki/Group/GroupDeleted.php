<?php

declare(strict_types=1);

namespace App\Events\Wiki\Group;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Group as GroupFilament;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Group;
use App\Models\Wiki\Video;

/**
 * Class GroupDeleted.
 *
 * @extends WikiDeletedEvent<Group>
 */
class GroupDeleted extends WikiDeletedEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(Group $group)
    {
        parent::__construct($group);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Group
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Group '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the filament notification.
     */
    protected function getNotificationMessage(): string
    {
        return "Group '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the Filament notification.
     */
    protected function getFilamentNotificationUrl(): string
    {
        $uriKey = GroupFilament::getRecordSlug();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }

    /**
     * Perform updates on related indices.
     */
    public function updateRelatedIndices(): void
    {
        $group = $this->getModel()->load(Group::RELATION_VIDEOS);

        $group->animethemes->each(function (AnimeTheme $theme) {
            $theme->searchable();
            $theme->animethemeentries->each(function (AnimeThemeEntry $entry) {
                $entry->searchable();
                $entry->videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
