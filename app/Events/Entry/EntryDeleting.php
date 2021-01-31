<?php

namespace App\Events\Entry;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Models\Video;

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

        // refresh video documents by detaching entry
        $videos = $entry->videos;
        $entry->videos()->detach();
        $videos->each(function (Video $video) {
            $video->searchable();
        });
    }
}
