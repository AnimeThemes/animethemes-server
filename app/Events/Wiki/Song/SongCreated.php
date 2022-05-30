<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Video;

/**
 * Class SongCreated.
 *
 * @extends WikiCreatedEvent<Song>
 */
class SongCreated extends WikiCreatedEvent implements UpdateRelatedIndicesEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Song  $song
     */
    public function __construct(Song $song)
    {
        parent::__construct($song);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Song
     */
    public function getModel(): Song
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
        return "Song '**{$this->getModel()->getName()}**' has been created.";
    }

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
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
