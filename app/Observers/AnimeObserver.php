<?php

namespace App\Observers;

use App\Models\Anime;

class AnimeObserver
{
    /**
     * Handle the anime "created" event.
     *
     * @param  \App\Models\Anime  $anime
     * @return void
     */
    public function created(Anime $anime)
    {
        $this->updateRelatedScoutIndices($anime);
    }

    /**
     * Handle the anime "updated" event.
     *
     * @param  \App\Models\Anime  $anime
     * @return void
     */
    public function updated(Anime $anime)
    {
        $this->updateRelatedScoutIndices($anime);
    }

    /**
     * Handle the anime "deleted" event.
     *
     * @param  \App\Models\Anime  $anime
     * @return void
     */
    public function deleted(Anime $anime)
    {
        $this->updateRelatedScoutIndices($anime);
    }

    /**
     * Handle updating of related index documents
     *
     * @param  \App\Models\Anime  $anime
     * @return void
     */
    private function updateRelatedScoutIndices(Anime $anime) {
        $anime->themes->each(function ($theme) {
            $theme->searchable();
            $theme->entries->each(function ($entry) {
                $entry->searchable();
                $entry->videos->searchable();
            });
        });
    }
}
