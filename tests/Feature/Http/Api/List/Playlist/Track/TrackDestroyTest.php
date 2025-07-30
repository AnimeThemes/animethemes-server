<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('protected', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class);

    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $track->playlist, 'track' => $track]));

    $response->assertUnauthorized();
});

test('forbidden if missing permission', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class);

    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $track->playlist, 'track' => $track]));

    $response->assertForbidden();
});

test('forbidden if not own playlist', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class);

    $track = PlaylistTrack::factory()
        ->for(Playlist::factory()->for(User::factory()))
        ->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $track->playlist, 'track' => $track]));

    $response->assertForbidden();
});

test('forbidden if flag disabled', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::deactivate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

    $response->assertForbidden();
});

test('scoped', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->has(PlaylistTrack::factory()->count(fake()->randomDigitNotNull()), Playlist::RELATION_TRACKS)
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $track = PlaylistTrack::factory()
        ->for(Playlist::factory()->for(User::factory()))
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

    $response->assertNotFound();
});

test('deleted', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

    $response->assertOk();

    $playlist->refresh();

    static::assertModelMissing($track);
    static::assertTrue($playlist->first()->doesntExist());
    static::assertTrue($playlist->last()->doesntExist());
});

test('destroy permitted for bypass', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class, fake()->boolean());

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(PlaylistTrack::class),
            SpecialPermission::BYPASS_FEATURE_FLAGS->value
        )
        ->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

    $response->assertOk();
});

test('destroy first', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->tracks(fake()->numberBetween(3, 9))
        ->createOne();

    $first = $playlist->first;
    $second = $first->next;

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $first]));

    $response->assertOk();

    $playlist->refresh();
    $second->refresh();

    static::assertModelMissing($first);
    static::assertTrue($playlist->first()->is($second));
    static::assertTrue($second->previous()->doesntExist());
});

test('destroy last', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->tracks(fake()->numberBetween(3, 9))
        ->createOne();

    $last = $playlist->last;
    $previous = $last->previous;

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $last]));

    $response->assertOk();

    $playlist->refresh();
    $previous->refresh();

    static::assertModelMissing($last);
    static::assertTrue($playlist->last()->is($previous));
    static::assertTrue($previous->next()->doesntExist());
});

test('destroy second', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->tracks(3)
        ->createOne();

    $first = $playlist->first;
    $second = $first->next;
    $third = $playlist->last;

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $second]));

    $response->assertOk();

    $playlist->refresh();
    $first->refresh();
    $third->refresh();

    static::assertModelMissing($second);

    static::assertTrue($playlist->first()->is($first));
    static::assertTrue($playlist->last()->is($third));

    static::assertTrue($first->previous()->doesntExist());
    static::assertTrue($first->next()->is($third));

    static::assertTrue($third->previous()->is($first));
    static::assertTrue($third->next()->doesntExist());
});
