<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Video;

/**
 * Class SongDeleting.
 */
class SongDeleting extends SongEvent implements UpdateRelatedIndicesEvent
{
    /**
     * Perform cascading deletes.
     *
     * @return void
     */
    public function updateRelatedIndices()
    {
        $song = $this->getSong()->load(['artists', 'animethemes.animethemeentries.videos']);

        if ($song->isForceDeleting()) {
            // refresh artist documents by detaching song
            $artists = $song->artists;
            $song->artists()->detach();
            $artists->each(function (Artist $artist) {
                $artist->searchable();
            });

            // refresh theme documents by dissociating song
            $song->animethemes->each(function (AnimeTheme $theme) {
                AnimeTheme::withoutEvents(function () use ($theme) {
                    $theme->song()->dissociate();
                    $theme->save();
                });
                $theme->searchable();
                $theme->animethemeentries->each(function (AnimeThemeEntry $entry) {
                    $entry->searchable();
                    $entry->videos->each(function (Video $video) {
                        $video->searchable();
                    });
                });
            });
        }
    }
}
