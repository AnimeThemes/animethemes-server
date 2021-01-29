<?php

namespace App\Providers;

use App\Events\Anime\AnimeCreated;
use App\Events\Anime\AnimeDeleted;
use App\Events\Anime\AnimeDeleting;
use App\Events\Anime\AnimeUpdated;
use App\Events\Announcement\AnnouncementCreated;
use App\Events\Announcement\AnnouncementDeleted;
use App\Events\Announcement\AnnouncementUpdated;
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
use App\Events\Invitation\InvitationDeleted;
use App\Events\Invitation\InvitationUpdated;
use App\Events\Pivot\AnimeImage\AnimeImageCreated;
use App\Events\Pivot\AnimeImage\AnimeImageDeleted;
use App\Events\Pivot\AnimeResource\AnimeResourceCreated;
use App\Events\Pivot\AnimeResource\AnimeResourceDeleted;
use App\Events\Pivot\AnimeResource\AnimeResourceUpdated;
use App\Events\Pivot\AnimeSeries\AnimeSeriesCreated;
use App\Events\Pivot\AnimeSeries\AnimeSeriesDeleted;
use App\Events\Pivot\ArtistImage\ArtistImageCreated;
use App\Events\Pivot\ArtistImage\ArtistImageDeleted;
use App\Events\Pivot\ArtistMember\ArtistMemberCreated;
use App\Events\Pivot\ArtistMember\ArtistMemberDeleted;
use App\Events\Pivot\ArtistMember\ArtistMemberUpdated;
use App\Events\Pivot\ArtistResource\ArtistResourceCreated;
use App\Events\Pivot\ArtistResource\ArtistResourceDeleted;
use App\Events\Pivot\ArtistResource\ArtistResourceUpdated;
use App\Events\Pivot\ArtistSong\ArtistSongCreated;
use App\Events\Pivot\ArtistSong\ArtistSongDeleted;
use App\Events\Pivot\ArtistSong\ArtistSongUpdated;
use App\Events\Pivot\VideoEntry\VideoEntryCreated;
use App\Events\Pivot\VideoEntry\VideoEntryDeleted;
use App\Events\Series\SeriesCreated;
use App\Events\Series\SeriesDeleted;
use App\Events\Series\SeriesUpdated;
use App\Events\Song\SongCreated;
use App\Events\Song\SongDeleted;
use App\Events\Song\SongDeleting;
use App\Events\Song\SongUpdated;
use App\Events\Synonym\SynonymCreated;
use App\Events\Synonym\SynonymDeleted;
use App\Events\Synonym\SynonymUpdated;
use App\Events\Theme\ThemeCreated;
use App\Events\Theme\ThemeCreating;
use App\Events\Theme\ThemeDeleted;
use App\Events\Theme\ThemeDeleting;
use App\Events\Theme\ThemeUpdated;
use App\Events\Video\VideoCreated;
use App\Events\Video\VideoCreating;
use App\Events\Video\VideoDeleted;
use App\Events\Video\VideoUpdated;
use App\Listeners\CascadesDeletes;
use App\Listeners\Image\RemoveImageFromStorage;
use App\Listeners\Invitation\CreateInvitationToken;
use App\Listeners\Invitation\SendInvitationMail;
use App\Listeners\SendDiscordNotification;
use App\Listeners\Theme\CreateThemeSlug;
use App\Listeners\UpdateRelatedIndices;
use App\Listeners\Video\InitializeVideoTags;
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
        AnimeImageCreated::class => [
            SendDiscordNotification::class,
        ],
        AnimeImageDeleted::class => [
            SendDiscordNotification::class,
        ],
        AnimeResourceCreated::class => [
            SendDiscordNotification::class,
        ],
        AnimeResourceDeleted::class => [
            SendDiscordNotification::class,
        ],
        AnimeResourceUpdated::class => [
            SendDiscordNotification::class,
        ],
        AnimeSeriesCreated::class => [
            SendDiscordNotification::class,
        ],
        AnimeSeriesDeleted::class => [
            SendDiscordNotification::class,
        ],
        AnimeUpdated::class => [
            UpdateRelatedIndices::class,
            SendDiscordNotification::class,
        ],
        AnnouncementCreated::class => [
            SendDiscordNotification::class,
        ],
        AnnouncementDeleted::class => [
            SendDiscordNotification::class,
        ],
        AnnouncementUpdated::class => [
            SendDiscordNotification::class,
        ],
        ArtistCreated::class => [
            SendDiscordNotification::class,
        ],
        ArtistDeleted::class => [
            SendDiscordNotification::class,
        ],
        ArtistImageCreated::class => [
            SendDiscordNotification::class,
        ],
        ArtistImageDeleted::class => [
            SendDiscordNotification::class,
        ],
        ArtistMemberCreated::class => [
            SendDiscordNotification::class,
        ],
        ArtistMemberDeleted::class => [
            SendDiscordNotification::class,
        ],
        ArtistMemberUpdated::class => [
            SendDiscordNotification::class,
        ],
        ArtistResourceCreated::class => [
            SendDiscordNotification::class,
        ],
        ArtistResourceDeleted::class => [
            SendDiscordNotification::class,
        ],
        ArtistResourceUpdated::class => [
            SendDiscordNotification::class,
        ],
        ArtistSongCreated::class => [
            UpdateRelatedIndices::class,
            SendDiscordNotification::class,
        ],
        ArtistSongDeleted::class => [
            UpdateRelatedIndices::class,
            SendDiscordNotification::class,
        ],
        ArtistSongUpdated::class => [
            UpdateRelatedIndices::class,
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
            SendDiscordNotification::class,
        ],
        InvitationCreating::class => [
            CreateInvitationToken::class,
        ],
        InvitationDeleted::class => [
            SendDiscordNotification::class,
        ],
        InvitationUpdated::class => [
            SendDiscordNotification::class,
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
        SongCreated::class => [
            UpdateRelatedIndices::class,
            SendDiscordNotification::class,
        ],
        SongDeleted::class => [
            SendDiscordNotification::class,
        ],
        SongDeleting::class => [
            CascadesDeletes::class,
        ],
        SongUpdated::class => [
            UpdateRelatedIndices::class,
            SendDiscordNotification::class,
        ],
        SynonymCreated::class => [
            UpdateRelatedIndices::class,
            SendDiscordNotification::class,
        ],
        SynonymDeleted::class => [
            UpdateRelatedIndices::class,
            SendDiscordNotification::class,
        ],
        SynonymUpdated::class => [
            UpdateRelatedIndices::class,
            SendDiscordNotification::class,
        ],
        ThemeCreated::class => [
            UpdateRelatedIndices::class,
            SendDiscordNotification::class,
        ],
        ThemeCreating::class => [
            CreateThemeSlug::class,
        ],
        ThemeDeleted::class => [
            SendDiscordNotification::class,
        ],
        ThemeDeleting::class => [
            CascadesDeletes::class,
        ],
        ThemeUpdated::class => [
            UpdateRelatedIndices::class,
            SendDiscordNotification::class,
        ],
        VideoCreated::class => [
            SendDiscordNotification::class,
        ],
        VideoCreating::class => [
            InitializeVideoTags::class,
        ],
        VideoDeleted::class => [
            SendDiscordNotification::class,
        ],
        VideoEntryCreated::class => [
            UpdateRelatedIndices::class,
            SendDiscordNotification::class,
        ],
        VideoEntryDeleted::class => [
            UpdateRelatedIndices::class,
            SendDiscordNotification::class,
        ],
        VideoUpdated::class => [
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
