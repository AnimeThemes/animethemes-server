<?php

namespace App\Events\Song;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Models\Artist;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\Video;

class SongDeleting extends SongEvent implements CascadesDeletesEvent
{
    /**
     * Perform cascading deletes.
     *
     * @return void
     */
    public function cascadeDeletes()
    {
        $song = $this->getSong();

        // refresh artist documents by detaching song
        $artists = $song->artists;
        $song->artists()->detach();
        $artists->each(function (Artist $artist) {
            $artist->searchable();
        });

        // refresh theme documents by dissociating song
        $song->themes->each(function (Theme $theme) {
            Theme::withoutEvents(function () use ($theme) {
                $theme->song()->dissociate();
                $theme->save();
            });
            $theme->searchable();
            $theme->entries->each(function (Entry $entry) {
                $entry->searchable();
                $entry->videos->each(function (Video $video) {
                    $video->searchable();
                });
            });
        });
    }
}
