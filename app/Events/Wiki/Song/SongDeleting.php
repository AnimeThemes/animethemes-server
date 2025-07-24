<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Video;

/**
 * Class SongDeleting.
 *
 * @extends BaseEvent<Song>
 */
class SongDeleting extends BaseEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(Song $song)
    {
        parent::__construct($song);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Song
    {
        return $this->model;
    }

    /**
     * Perform cascading deletes.
     */
    public function updateRelatedIndices(): void
    {
        $song = $this->getModel()->load([Song::RELATION_ARTISTS, Song::RELATION_VIDEOS]);

        if ($song->isForceDeleting()) {
            // refresh artist documents by detaching song
            $artists = $song->artists;
            $song->artists()->detach();
            $artists->each(fn (Artist $artist) => $artist->searchable());

            // refresh theme documents by dissociating song
            $song->animethemes->each(function (AnimeTheme $theme) {
                AnimeTheme::withoutEvents(function () use ($theme) {
                    $theme->song()->dissociate();
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
