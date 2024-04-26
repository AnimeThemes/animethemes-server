<?php

declare(strict_types=1);

namespace App\Events\Wiki\Group;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Group;
use App\Models\Wiki\Video;

/**
 * Class GroupDeleting.
 *
 * @extends BaseEvent<Group>
 */
class GroupDeleting extends BaseEvent implements UpdateRelatedIndicesEvent
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
     * Perform cascading deletes.
     *
     * @return void
     */
    public function updateRelatedIndices(): void
    {
        $group = $this->getModel()->load(Group::RELATION_VIDEOS);

        if ($group->isForceDeleting()) {
            $group->animethemes->each(function (AnimeTheme $theme) {
                AnimeTheme::withoutEvents(function () use ($theme) {
                    $theme->theme_group()->dissociate();
                    $theme->save();
                });
                $theme->searchable();
                $theme->animethemeentries->each(function (AnimeThemeEntry $entry) {
                    $entry->searchable();
                    $entry->videos->each(fn (Video $video) => $video->searchable());
                });
            });
        }
    }
}
