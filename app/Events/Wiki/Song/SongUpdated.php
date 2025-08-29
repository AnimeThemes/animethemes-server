<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Video;

/**
 * @extends WikiUpdatedEvent<Song>
 */
class SongUpdated extends WikiUpdatedEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(Song $song)
    {
        parent::__construct($song);
        $this->initializeEmbedFields($song);
    }

    public function getModel(): Song
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Song '**{$this->getModel()->getName()}**' has been updated.";
    }

    public function updateRelatedIndices(): void
    {
        $song = $this->getModel()->load([Song::RELATION_ARTISTS, Song::RELATION_VIDEOS]);

        $song->artists->each(fn (Artist $artist) => $artist->searchable());

        $song->animethemes->each(function (AnimeTheme $theme) {
            $theme->searchable();
            $theme->animethemeentries->each(function (AnimeThemeEntry $entry) {
                $entry->searchable();
                $entry->videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
