<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist\Track;

use App\Constants\Config\PlaylistConstants;
use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class TrackStoreTest.
 */
class TrackStoreTest extends TestCase
{
    use WithFaker;

    /**
     * The Track Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $playlist = Playlist::factory()->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideo->animethemeentry)
            ->for($entryVideo->video)
            ->makeOne();

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Track Store Endpoint shall forbid users without the create playlist track permission.
     *
     * @return void
     */
    public function testForbiddenIfMissingPermission(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $playlist = Playlist::factory()->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideo->animethemeentry)
            ->for($entryVideo->video)
            ->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Track Store Endpoint shall forbid users from creating a track if they don't own the playlist.
     *
     * @return void
     */
    public function testForbiddenIfNotOwnPlaylist(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideo->animethemeentry)
            ->for($entryVideo->video)
            ->makeOne();

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Track Store Endpoint shall forbid users from creating playlist tracks
     * if the Allow Playlist Management feature is inactive.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::deactivate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideo->animethemeentry)
            ->for($entryVideo->video)
            ->makeOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Track Store Endpoint shall require the entry & video field.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist]));

        $response->assertJsonValidationErrors([
            PlaylistTrack::ATTRIBUTE_ENTRY,
            PlaylistTrack::ATTRIBUTE_VIDEO,
        ]);
    }

    /**
     * The Playlist Track Store Endpoint shall require the entry and video to have an association.
     *
     * @return void
     */
    public function testAnimeThemeEntryVideoExists(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->create();

        $video = Video::factory()->create();

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $parameters = PlaylistTrack::factory()
            ->for($playlist)
            ->raw([
                PlaylistTrack::ATTRIBUTE_ENTRY => $entry->getKey(),
                PlaylistTrack::ATTRIBUTE_VIDEO => $video->getKey(),
            ]);

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', $parameters));

        $response->assertJsonValidationErrors([
            PlaylistTrack::ATTRIBUTE_ENTRY,
            PlaylistTrack::ATTRIBUTE_VIDEO,
        ]);
    }

    /**
     * The Track Store Endpoint shall prohibit the next and previous fields from both being present.
     *
     * @return void
     */
    public function testProhibitsNextAndPrevious(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

        $entryVideoPrevious = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $entryVideoNext = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $previous = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideoPrevious->animethemeentry)
            ->for($entryVideoPrevious->video)
            ->createOne();

        $next = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideoNext->animethemeentry)
            ->for($entryVideoNext->video)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideo->animethemeentry)
            ->for($entryVideo->video)
            ->makeOne([
                PlaylistTrack::RELATION_PREVIOUS => $previous->getRouteKey(),
                PlaylistTrack::RELATION_NEXT => $next->getRouteKey(),
            ]);

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertJsonValidationErrors([
            PlaylistTrack::RELATION_NEXT,
            PlaylistTrack::RELATION_PREVIOUS,
        ]);
    }

    /**
     * The Track Store Endpoint shall restrict the next track to a track within the playlist.
     *
     * @return void
     */
    public function testScopeNext(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

        $entryVideoNext = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $next = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->for($entryVideoNext->animethemeentry)
            ->for($entryVideoNext->video)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideo->animethemeentry)
            ->for($entryVideo->video)
            ->makeOne([
                PlaylistTrack::RELATION_NEXT => $next->getRouteKey(),
            ]);

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertJsonValidationErrors([
            PlaylistTrack::RELATION_NEXT,
        ]);
    }

    /**
     * The Track Store Endpoint shall restrict the next track to a track within the playlist.
     *
     * @return void
     */
    public function testScopePrevious(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

        $entryVideoPrevious = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $previous = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->for($entryVideoPrevious->animethemeentry)
            ->for($entryVideoPrevious->video)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideo->animethemeentry)
            ->for($entryVideo->video)
            ->makeOne([
                PlaylistTrack::RELATION_PREVIOUS => $previous->getRouteKey(),
            ]);

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertJsonValidationErrors([
            PlaylistTrack::RELATION_PREVIOUS,
        ]);
    }

    /**
     * The Track Store Endpoint shall create a playlist track.
     *
     * @return void
     */
    public function testCreate(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideo->animethemeentry)
            ->for($entryVideo->video)
            ->makeOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertCreated();

        $track = PlaylistTrack::query()->first();
        $playlist->refresh();

        static::assertDatabaseCount(PlaylistTrack::class, 1);

        static::assertTrue($playlist->first()->is($track));
        static::assertTrue($playlist->last()->is($track));
    }

    /**
     * The Track Store Endpoint shall allow inserting after tracks including the last track.
     *
     * @return void
     */
    public function testCreateAfterLastTrack(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $trackCount = $this->faker->numberBetween(2, 9);

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($trackCount)
            ->createOne();

        $last = $playlist->last;

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideo->animethemeentry)
            ->for($entryVideo->video)
            ->makeOne([
                PlaylistTrack::RELATION_PREVIOUS => $last->getRouteKey(),
            ]);

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertCreated();

        /** @var PlaylistTrack $track */
        $track = PlaylistTrack::query()->latest()->first();
        $playlist->refresh();
        $last->refresh();

        static::assertDatabaseCount(PlaylistTrack::class, $trackCount + 1);

        static::assertTrue($playlist->last()->is($track));

        static::assertTrue($last->next()->is($track));

        static::assertTrue($track->previous()->is($last));
        static::assertTrue($track->next()->doesntExist());
    }

    /**
     * The Track Store Endpoint shall allow inserting after tracks including the first track.
     *
     * @return void
     */
    public function testCreateAfterFirstTrack(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $trackCount = $this->faker->numberBetween(2, 9);

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($trackCount)
            ->createOne();

        $first = $playlist->first;
        $next = $first->next;

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideo->animethemeentry)
            ->for($entryVideo->video)
            ->makeOne([
                PlaylistTrack::RELATION_PREVIOUS => $first->getRouteKey(),
            ]);

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertCreated();

        /** @var PlaylistTrack $track */
        $track = PlaylistTrack::query()->latest()->first();
        $playlist->refresh();
        $first->refresh();

        static::assertDatabaseCount(PlaylistTrack::class, $trackCount + 1);

        static::assertTrue($playlist->first()->is($first));

        static::assertTrue($first->next()->is($track));

        static::assertTrue($track->previous()->is($first));
        static::assertTrue($track->next()->is($next));
    }

    /**
     * The Track Store Endpoint shall allow inserting before tracks including the last track.
     *
     * @return void
     */
    public function testCreateBeforeLastTrack(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $trackCount = $this->faker->numberBetween(2, 9);

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($trackCount)
            ->createOne();

        $last = $playlist->last;
        $previous = $last->previous;

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideo->animethemeentry)
            ->for($entryVideo->video)
            ->makeOne([
                PlaylistTrack::RELATION_NEXT => $last->getRouteKey(),
            ]);

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertCreated();

        /** @var PlaylistTrack $track */
        $track = PlaylistTrack::query()->latest()->first();
        $playlist->refresh();
        $last->refresh();

        static::assertDatabaseCount(PlaylistTrack::class, $trackCount + 1);

        static::assertTrue($playlist->last()->is($last));

        static::assertTrue($last->previous()->is($track));

        static::assertTrue($track->previous()->is($previous));
        static::assertTrue($track->next()->is($last));
    }

    /**
     * The Track Store Endpoint shall allow inserting before tracks including the first track.
     *
     * @return void
     */
    public function testCreateBeforeFirstTrack(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $trackCount = $this->faker->numberBetween(2, 9);

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($trackCount)
            ->createOne();

        $first = $playlist->first;

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideo->animethemeentry)
            ->for($entryVideo->video)
            ->makeOne([
                PlaylistTrack::RELATION_NEXT => $first->getRouteKey(),
            ]);

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertCreated();

        /** @var PlaylistTrack $track */
        $track = PlaylistTrack::query()->latest()->first();
        $playlist->refresh();
        $first->refresh();

        static::assertDatabaseCount(PlaylistTrack::class, $trackCount + 1);

        static::assertTrue($playlist->first()->is($track));

        static::assertTrue($track->previous()->doesntExist());
        static::assertTrue($track->next()->is($first));
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to create playlist tracks
     * even if the Allow Playlist Management feature is inactive.
     *
     * @return void
     */
    public function testCreatePermittedForBypass(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE->format(PlaylistTrack::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS->value
            )
            ->createOne();

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideo->animethemeentry)
            ->for($entryVideo->video)
            ->makeOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertCreated();
    }

    /**
     * The Track Store Endpoint shall forbid users from creating playlists that exceed the max track limit.
     *
     * @return void
     */
    public function testMaxTrackLimit(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        $trackLimit = $this->faker->randomDigitNotNull();

        Config::set(PlaylistConstants::MAX_TRACKS_QUALIFIED, $trackLimit);
        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $playlist = Playlist::factory()
            ->tracks($trackLimit)
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideo->animethemeentry)
            ->for($entryVideo->video)
            ->makeOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Track Store Endpoint shall permit users with bypass feature flag permission
     * to create playlists that exceed the max track limit.
     *
     * @return void
     */
    public function testMaxTrackLimitPermittedForBypass(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        $trackLimit = $this->faker->randomDigitNotNull();

        Config::set(PlaylistConstants::MAX_TRACKS_QUALIFIED, $trackLimit);
        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE->format(PlaylistTrack::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS->value
            )
            ->createOne();

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $playlist = Playlist::factory()
            ->tracks($trackLimit)
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for($entryVideo->animethemeentry)
            ->for($entryVideo->video)
            ->makeOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertCreated();
    }
}
