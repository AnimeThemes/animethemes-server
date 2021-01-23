<?php

namespace App\Providers;

use App\Models\Image;
use App\Models\Invitation;
use App\Models\Song;
use App\Models\Synonym;
use App\Models\Theme;
use App\Models\Video;
use App\Observers\ImageObserver;
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
        Image::observe(ImageObserver::class);
        Invitation::observe(InvitationObserver::class);
        Song::observe(SongObserver::class);
        Synonym::observe(SynonymObserver::class);
        Theme::observe(ThemeObserver::class);
        Video::observe(VideoObserver::class);
    }
}
