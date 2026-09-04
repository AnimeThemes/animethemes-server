<?php

declare(strict_types=1);

namespace App\Events\Wiki\Theme;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;

/**
 * @extends WikiCreatedEvent<Theme>
 */
class ThemeCreated extends WikiCreatedEvent implements UpdateRelatedIndicesEvent
{
    public function updateRelatedIndices(): void
    {
        $theme = $this->getModel()->load(Theme::RELATION_VIDEOS);

        $theme->animethemeentries->each(function (Entry $entry): void {
            $entry->searchable();
            $entry->videos->each(fn (Video $video) => $video->searchable());
        });
    }
}
