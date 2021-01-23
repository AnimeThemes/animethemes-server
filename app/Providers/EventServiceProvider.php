<?php

namespace App\Providers;

use App\Events\Anime\AnimeCreated;
use App\Events\Anime\AnimeDeleted;
use App\Events\Anime\AnimeDeleting;
use App\Events\Anime\AnimeUpdated;
use App\Events\Artist\ArtistCreated;
use App\Events\Artist\ArtistDeleted;
use App\Events\Artist\ArtistUpdated;
use App\Events\Entry\EntryCreated;
use App\Events\Entry\EntryDeleted;
use App\Events\Entry\EntryDeleting;
use App\Events\Entry\EntryUpdated;
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
        AnimeCreated::class => [
            UpdateRelatedIndices::class,
            SendDiscordNotification::class,
        ],
        AnimeDeleted::class => [
            SendDiscordNotification::class,
        ],
        AnimeDeleting::class => [
            CascadesDeletes::class,
        ],
        AnimeUpdated::class => [
            UpdateRelatedIndices::class,
            SendDiscordNotification::class,
        ],
        ArtistCreated::class => [
            SendDiscordNotification::class,
        ],
        ArtistDeleted::class => [
            SendDiscordNotification::class,
        ],
        ArtistUpdated::class => [
            SendDiscordNotification::class,
        ],
        EntryCreated::class => [
            SendDiscordNotification::class,
            UpdateRelatedIndices::class,
        ],
        EntryDeleted::class => [
            SendDiscordNotification::class,
        ],
        EntryDeleting::class => [
            CascadesDeletes::class,
        ],
        EntryUpdated::class => [
            SendDiscordNotification::class,
            UpdateRelatedIndices::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
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
