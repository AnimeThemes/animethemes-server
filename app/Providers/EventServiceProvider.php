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
use App\Events\ExternalResource\ExternalResourceCreated;
use App\Events\ExternalResource\ExternalResourceDeleted;
use App\Events\ExternalResource\ExternalResourceUpdated;
use App\Events\Image\ImageCreated;
use App\Events\Image\ImageDeleted;
use App\Events\Image\ImageUpdated;
use App\Events\Invitation\InvitationCreated;
use App\Events\Invitation\InvitationCreating;
use App\Events\Series\SeriesCreated;
use App\Events\Series\SeriesDeleted;
use App\Events\Series\SeriesUpdated;
use App\Listeners\CascadesDeletes;
use App\Listeners\Image\RemoveImageFromStorage;
use App\Listeners\Invitation\CreateInvitationToken;
use App\Listeners\Invitation\SendInvitationMail;
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
        ExternalResourceCreated::class => [
            SendDiscordNotification::class,
        ],
        ExternalResourceDeleted::class => [
            SendDiscordNotification::class,
        ],
        ExternalResourceUpdated::class => [
            SendDiscordNotification::class,
        ],
        ImageCreated::class => [
            SendDiscordNotification::class,
        ],
        ImageDeleted::class => [
            SendDiscordNotification::class,
            RemoveImageFromStorage::class,
        ],
        ImageUpdated::class => [
            SendDiscordNotification::class,
        ],
        InvitationCreated::class => [
            SendInvitationMail::class,
        ],
        InvitationCreating::class => [
            CreateInvitationToken::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SeriesCreated::class => [
            SendDiscordNotification::class,
        ],
        SeriesDeleted::class => [
            SendDiscordNotification::class,
        ],
        SeriesUpdated::class => [
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
