<?php

namespace App\Observers;

use App\Models\Synonym;

class SynonymObserver
{
    /**
     * Handle the synonym "created" event.
     *
     * @param  \App\Models\Synonym  $synonym
     * @return void
     */
    public function created(Synonym $synonym)
    {
        $this->updateRelatedScoutIndices($synonym);
    }

    /**
     * Handle the synonym "updated" event.
     *
     * @param  \App\Models\Synonym  $synonym
     * @return void
     */
    public function updated(Synonym $synonym)
    {
        $this->updateRelatedScoutIndices($synonym);
    }

    /**
     * Handle the synonym "deleted" event.
     *
     * @param  \App\Models\Synonym  $synonym
     * @return void
     */
    public function deleted(Synonym $synonym)
    {
        $this->updateRelatedScoutIndices($synonym);
    }

    /**
     * Handle updating of related index documents
     *
     * @param  \App\Models\Synonym  $synonym
     * @return void
     */
    private function updateRelatedScoutIndices(Synonym $synonym) : void {
        $synonym->anime->searchable();
        $synonym->anime->themes->each(function ($theme) {
            $theme->searchable();
            $theme->entries->each(function ($entry) {
                $entry->searchable();
                $entry->videos->searchable();
            });
        });
    }
}
