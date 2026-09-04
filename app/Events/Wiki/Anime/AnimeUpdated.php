<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;

/**
 * @extends WikiUpdatedEvent<Anime>
 */
class AnimeUpdated extends WikiUpdatedEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(Anime $anime)
    {
        parent::__construct($anime);
        $this->initializeEmbedFields($anime);
    }

    public function updateRelatedIndices(): void
    {
        $anime = $this->getModel()->load(Anime::RELATION_VIDEOS);

        $anime->animethemes->each(function (Theme $theme): void {
            $theme->searchable();
            $theme->animethemeentries->each(function (Entry $entry): void {
                $entry->searchable();
                $entry->videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
