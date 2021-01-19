<?php

namespace App\Providers;

use App\Events\Anime\AnimeCreated;
use App\Events\Anime\AnimeDeleted;
use App\Events\Anime\AnimeUpdated;
use App\Listeners\SendDiscordNotification;
use App\Listeners\UpdateAnimeRelatedScoutIndices;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        AnimeCreated::class => [
            UpdateAnimeRelatedScoutIndices::class,
            SendDiscordNotification::class,
        ],
        AnimeUpdated::class => [
            UpdateAnimeRelatedScoutIndices::class,
            SendDiscordNotification::class,
        ],
        AnimeDeleted::class => [
            UpdateAnimeRelatedScoutIndices::class,
            SendDiscordNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
