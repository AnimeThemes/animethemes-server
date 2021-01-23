<?php

namespace App\Events\Entry;

use App\Events\CascadesDeletesEvent;

class EntryDeleting extends EntryEvent implements CascadesDeletesEvent
{
    /**
     * Perform cascading deletes.
     *
     * @return void
     */
    public function cascadeDeletes()
    {
        $entry = $this->getEntry();

        $videos = $entry->videos;

        $entry->videos()->detach();

        $videos->searchable();
    }
}
