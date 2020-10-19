<?php

namespace App\Observers;

use App\Models\Song;

class SongObserver
{
    /**
     * Handle the song "created" event.
     *
     * @param  \App\Models\Song  $song
     * @return void
     */
    public function created(Song $song)
    {
        $this->updateRelatedScoutIndices($song);
    }

    /**
     * Handle the song "updated" event.
     *
     * @param  \App\Models\Song  $song
     * @return void
     */
    public function updated(Song $song)
    {
        $this->updateRelatedScoutIndices($song);
    }

    /**
     * Handle the song "deleted" event.
     *
     * @param  \App\Models\Song  $song
     * @return void
     */
    public function deleted(Song $song)
    {
        $this->updateRelatedScoutIndices($song);
    }

    /**
     * Handle updating of related index documents.
     *
     * @param  \App\Models\Song  $song
     * @return void
     */
    private function updateRelatedScoutIndices(Song $song)
    {
        $song->artists->searchable();
        $song->themes->each(function ($theme) {
            $theme->searchable();
            $theme->entries->each(function ($entry) {
                $entry->searchable();
                $entry->videos->searchable();
            });
        });
    }
}
