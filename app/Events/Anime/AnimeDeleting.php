<?php

namespace App\Events\Anime;

use App\Events\CascadesDeletesEvent;
use App\Models\Entry;

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

        $anime->synonyms->each(function ($synonym) {
            $synonym->delete();
        });

        $anime->themes->each(function ($theme) {
            $theme->entries->each(function (Entry $entry) {
                Entry::withoutEvents(function () use ($entry) {
                    $entry->unsearchable();
                    $entry->delete();
                });
            });
            $theme->delete();
        });
    }
}
