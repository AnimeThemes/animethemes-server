<?php

declare(strict_types=1);

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
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('protected', function () {
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

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

    $response->assertUnauthorized();
});

test('forbidden if missing permission', function () {
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

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

    $response->assertForbidden();
});

test('forbidden if not own playlist', function () {
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

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

    $response->assertForbidden();
});

test('forbidden if flag disabled', function () {
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

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

    $response->assertForbidden();
});

test('required fields', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist]));

    $response->assertJsonValidationErrors([
        PlaylistTrack::ATTRIBUTE_ENTRY,
        PlaylistTrack::ATTRIBUTE_VIDEO,
    ]);
});

test('anime theme entry video exists', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class);

    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->create();

    $video = Video::factory()->create();

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->makeOne([
            PlaylistTrack::ATTRIBUTE_ENTRY => $entry->getKey(),
            PlaylistTrack::ATTRIBUTE_VIDEO => $video->getKey(),
        ]);

    Sanctum::actingAs($user);

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

    $response->assertJsonValidationErrors([
        PlaylistTrack::ATTRIBUTE_ENTRY,
        PlaylistTrack::ATTRIBUTE_VIDEO,
    ]);
});

test('prohibits next and previous', function () {
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

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

    $response->assertJsonValidationErrors([
        PlaylistTrack::RELATION_NEXT,
        PlaylistTrack::RELATION_PREVIOUS,
    ]);
});

test('scope next', function () {
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

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

    $response->assertJsonValidationErrors([
        PlaylistTrack::RELATION_NEXT,
    ]);
});

test('scope previous', function () {
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

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

    $response->assertJsonValidationErrors([
        PlaylistTrack::RELATION_PREVIOUS,
    ]);
});

test('create', function () {
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

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

    $response->assertCreated();

    $track = PlaylistTrack::query()->first();
    $playlist->refresh();

    $this->assertDatabaseCount(PlaylistTrack::class, 1);

    $this->assertTrue($playlist->first()->is($track));
    $this->assertTrue($playlist->last()->is($track));
});

test('create after last track', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $trackCount = fake()->numberBetween(2, 9);

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

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

    $response->assertCreated();

    /** @var PlaylistTrack $track */
    $track = PlaylistTrack::query()->latest()->first();
    $playlist->refresh();
    $last->refresh();

    $this->assertDatabaseCount(PlaylistTrack::class, $trackCount + 1);

    $this->assertTrue($playlist->last()->is($track));

    $this->assertTrue($last->next()->is($track));

    $this->assertTrue($track->previous()->is($last));
    $this->assertTrue($track->next()->doesntExist());
});

test('create after first track', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $trackCount = fake()->numberBetween(2, 9);

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

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

    $response->assertCreated();

    /** @var PlaylistTrack $track */
    $track = PlaylistTrack::query()->latest()->first();
    $playlist->refresh();
    $first->refresh();

    $this->assertDatabaseCount(PlaylistTrack::class, $trackCount + 1);

    $this->assertTrue($playlist->first()->is($first));

    $this->assertTrue($first->next()->is($track));

    $this->assertTrue($track->previous()->is($first));
    $this->assertTrue($track->next()->is($next));
});

test('create before last track', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $trackCount = fake()->numberBetween(2, 9);

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

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

    $response->assertCreated();

    /** @var PlaylistTrack $track */
    $track = PlaylistTrack::query()->latest()->first();
    $playlist->refresh();
    $last->refresh();

    $this->assertDatabaseCount(PlaylistTrack::class, $trackCount + 1);

    $this->assertTrue($playlist->last()->is($last));

    $this->assertTrue($last->previous()->is($track));

    $this->assertTrue($track->previous()->is($previous));
    $this->assertTrue($track->next()->is($last));
});

test('create before first track', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(PlaylistTrack::class))->createOne();

    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $trackCount = fake()->numberBetween(2, 9);

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

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

    $response->assertCreated();

    /** @var PlaylistTrack $track */
    $track = PlaylistTrack::query()->latest()->first();
    $playlist->refresh();
    $first->refresh();

    $this->assertDatabaseCount(PlaylistTrack::class, $trackCount + 1);

    $this->assertTrue($playlist->first()->is($track));

    $this->assertTrue($track->previous()->doesntExist());
    $this->assertTrue($track->next()->is($first));
});

test('create permitted for bypass', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class, fake()->boolean());

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

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

    $response->assertCreated();
});

test('max track limit', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $trackLimit = fake()->randomDigitNotNull();

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

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

    $response->assertForbidden();
});

test('max track limit permitted for bypass', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $trackLimit = fake()->randomDigitNotNull();

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

    $response = post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

    $response->assertCreated();
});
