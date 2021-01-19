<?php

namespace App\Listeners;

use App\Events\Anime\AnimeEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateAnimeRelatedScoutIndices implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Events\Anime\AnimeEvent  $event
     * @return void
     */
    public function handle(AnimeEvent $event)
    {
        $anime = $event->getAnime();

        $anime->themes->each(function ($theme) {
            $theme->searchable();
            $theme->entries->each(function ($entry) {
                $entry->searchable();
                $entry->videos->searchable();
            });
        });
    }
}
