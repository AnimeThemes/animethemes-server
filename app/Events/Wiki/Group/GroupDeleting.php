<?php

declare(strict_types=1);

namespace App\Events\Wiki\Group;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Group;

/**
 * @extends BaseEvent<Group>
 */
class GroupDeleting extends BaseEvent implements UpdateRelatedIndicesEvent
{
    public function updateRelatedIndices(): void
    {
        $group = $this->getModel()->load(Group::RELATION_VIDEOS);

        if ($group->isForceDeleting()) {
            $group->animethemes->each(function (AnimeTheme $theme): void {
                AnimeTheme::withoutEvents(function () use ($theme): void {
                    $theme->group()->dissociate();
                    $theme->save();
                });
            });
        }
    }
}
