<?php

declare(strict_types=1);

namespace App\Events\Wiki\Group;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Group;
use App\Models\Wiki\Video;

/**
 * @extends WikiUpdatedEvent<Group>
 */
class GroupUpdated extends WikiUpdatedEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(Group $group)
    {
        parent::__construct($group);
        $this->initializeEmbedFields($group);
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Group '**{$this->getModel()->getName()}**' has been updated.";
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
