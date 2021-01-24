<?php

namespace App\Providers;

use App\Models\Synonym;
use App\Models\Theme;
use App\Models\Video;
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
        Synonym::observe(SynonymObserver::class);
        Theme::observe(ThemeObserver::class);
        Video::observe(VideoObserver::class);
    }
}
