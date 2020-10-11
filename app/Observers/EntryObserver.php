<?php

namespace App\Observers;

use App\Models\Entry;

class EntryObserver
{
    /**
     * Handle the entry "created" event.
     *
     * @param  \App\Models\Entry  $entry
     * @return void
     */
    public function created(Entry $entry)
    {
        $this->updateRelatedScoutIndices($entry);
    }

    /**
     * Handle the entry "updated" event.
     *
     * @param  \App\Models\Entry  $entry
     * @return void
     */
    public function updated(Entry $entry)
    {
        $this->updateRelatedScoutIndices($entry);
    }

    /**
     * Handle the entry "deleted" event.
     *
     * @param  \App\Models\Entry  $entry
     * @return void
     */
    public function deleted(Entry $entry)
    {
        $this->updateRelatedScoutIndices($entry);
    }

    /**
     * Handle updating of related index documents.
     *
     * @param  \App\Models\Entry  $entry
     * @return void
     */
    private function updateRelatedScoutIndices(Entry $entry) : void
    {
        $entry->videos->searchable();
    }
}
