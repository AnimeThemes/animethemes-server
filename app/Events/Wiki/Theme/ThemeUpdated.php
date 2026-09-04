<?php

declare(strict_types=1);

namespace App\Events\Wiki\Theme;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;

/**
 * @extends WikiUpdatedEvent<Theme>
 */
class ThemeUpdated extends WikiUpdatedEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(Theme $theme)
    {
        parent::__construct($theme);

        $this->initializeEmbedFields($theme);
    }

    public function updateRelatedIndices(): void
    {
        $theme = $this->getModel()->load(Theme::RELATION_VIDEOS);

        $theme->animethemeentries->each(function (Entry $entry): void {
            $entry->searchable();
            $entry->videos->each(fn (Video $video) => $video->searchable());
        });
    }
}
