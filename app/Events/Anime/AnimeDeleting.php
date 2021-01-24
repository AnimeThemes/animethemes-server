<?php

namespace App\Events\Anime;

use App\Events\CascadesDeletesEvent;
use App\Events\Entry\EntryDeleting;
use App\Models\Entry;
use App\Models\Synonym;
use App\Models\Theme;
use Illuminate\Support\Facades\Event;

class AnimeDeleting extends AnimeEvent implements CascadesDeletesEvent
{
    /**
     * Perform cascading deletes.
     *
     * @return void
     */
    public function cascadeDeletes()
    {
        $anime = $this->getAnime();

        $anime->synonyms->each(function (Synonym $synonym) {
            $synonym->delete();
        });

        $anime->themes->each(function (Theme $theme) {
            $theme->entries->each(function (Entry $entry) {
                Entry::withoutEvents(function () use ($entry) {
                    Event::until(new EntryDeleting($entry));
                    $entry->unsearchable();
                    $entry->delete();
                });
            });
            $theme->delete();
        });
    }
}
