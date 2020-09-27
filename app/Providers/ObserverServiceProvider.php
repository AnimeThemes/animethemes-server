<?php

namespace App\Providers;

use App\Models\Anime;
use App\Models\Entry;
use App\Models\Invitation;
use App\Models\Song;
use App\Models\Synonym;
use App\Models\Theme;
use App\Models\Video;
use App\Observers\AnimeObserver;
use App\Observers\EntryObserver;
use App\Observers\InvitationObserver;
use App\Observers\SongObserver;
use App\Observers\SynonymObserver;
use App\Observers\ThemeObserver;
use App\Observers\VideoObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Anime::observe(AnimeObserver::class);
        Entry::observe(EntryObserver::class);
        Invitation::observe(InvitationObserver::class);
        Song::observe(SongObserver::class);
        Synonym::observe(SynonymObserver::class);
        Theme::observe(ThemeObserver::class);
        Video::observe(VideoObserver::class);
    }
}
