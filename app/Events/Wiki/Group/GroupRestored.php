<?php

declare(strict_types=1);

namespace App\Events\Wiki\Group;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Group;
use App\Models\Wiki\Video;

/**
 * @extends WikiRestoredEvent<Group>
 */
class GroupRestored extends WikiRestoredEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(Group $group)
    {
        parent::__construct($group);
    }

    public function getModel(): Group
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Group '**{$this->getModel()->getName()}**' has been restored.";
    }

    public function updateRelatedIndices(): void
    {
        $group = $this->getModel()->load(Group::RELATION_VIDEOS);

        $group->animethemes->each(function (AnimeTheme $theme): void {
            $theme->searchable();
            $theme->animethemeentries->each(function (AnimeThemeEntry $entry): void {
                $entry->searchable();
                $entry->videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
