<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

/**
 * @extends WikiCreatedEvent<Anime>
 */
class AnimeCreated extends WikiCreatedEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(Anime $anime)
    {
        parent::__construct($anime);
    }

    public function getModel(): Anime
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Anime '**{$this->getModel()->getName()}**' has been created.";
    }

    public function updateRelatedIndices(): void
    {
        $anime = $this->getModel()->load(Anime::RELATION_VIDEOS);

        $anime->animethemes->each(function (AnimeTheme $theme) {
            $theme->searchable();
            $theme->animethemeentries->each(function (AnimeThemeEntry $entry) {
                $entry->searchable();
                $entry->videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
