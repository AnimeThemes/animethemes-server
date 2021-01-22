<?php

namespace App\Providers;

use App\Events\Anime\AnimeCreated;
use App\Events\Anime\AnimeDeleted;
use App\Events\Anime\AnimeDeleting;
use App\Events\Anime\AnimeUpdated;
use App\Listeners\CascadesDeletes;
use App\Listeners\SendDiscordNotification;
use App\Listeners\UpdateRelatedIndices;
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
            UpdateRelatedIndices::class,
            SendDiscordNotification::class,
        ],
        AnimeUpdated::class => [
            UpdateRelatedIndices::class,
            SendDiscordNotification::class,
        ],
        AnimeDeleting::class => [
            CascadesDeletes::class,
        ],
        AnimeDeleted::class => [
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
