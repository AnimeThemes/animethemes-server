<?php

namespace App\Events\Theme;

use App\Events\CascadesDeletesEvent;
use App\Events\Entry\EntryDeleting;
use App\Models\Entry;
use Illuminate\Support\Facades\Event;

class ThemeDeleting extends ThemeEvent implements CascadesDeletesEvent
{
    /**
     * Perform cascading deletes.
     *
     * @return void
     */
    public function cascadeDeletes()
    {
        $theme = $this->getTheme();

        $theme->entries->each(function (Entry $entry) {
            Entry::withoutEvents(function () use ($entry) {
                Event::until(new EntryDeleting($entry));
                $entry->unsearchable();
                $entry->delete();
            });
        });
    }
}
