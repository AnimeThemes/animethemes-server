<?php

namespace App\Events\Song;

use App\Events\CascadesDeletesEvent;
use App\Models\Entry;
use App\Models\Theme;

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
        $artists->searchable();

        // refresh theme documents by dissociating song
        $song->themes->each(function (Theme $theme) {
            Theme::withoutEvents(function () use ($theme) {
                $theme->song()->dissociate();
                $theme->save();
            });
            $theme->searchable();
            $theme->entries->each(function (Entry $entry) {
                $entry->searchable();
                $entry->videos->searchable();
            });
        });
    }
}
