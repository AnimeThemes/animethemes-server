<?php

declare(strict_types=1);

namespace App\Events\Wiki\Group;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Group;
use App\Models\Wiki\Video;

/**
 * Class GroupCreated.
 *
 * @extends WikiCreatedEvent<Group>
 */
class GroupCreated extends WikiCreatedEvent implements UpdateRelatedIndicesEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Group  $group
     */
    public function __construct(Group $group)
    {
        parent::__construct($group);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Group
     */
    public function getModel(): Group
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
        return "Group '**{$this->getModel()->getName()}**' has been created.";
    }

    /**
     * Perform updates on related indices.
     *
     * @return void
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
