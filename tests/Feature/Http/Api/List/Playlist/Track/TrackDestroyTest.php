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

use function Pest\Laravel\delete;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('protected', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class);

    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    $response = delete(route('api.playlist.track.destroy', ['playlist' => $track->playlist, 'track' => $track]));

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

    $response = delete(route('api.playlist.track.destroy', ['playlist' => $track->playlist, 'track' => $track]));

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

    $response = delete(route('api.playlist.track.destroy', ['playlist' => $track->playlist, 'track' => $track]));

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

    $response = delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

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

    $response = delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

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

    $response = delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

    $response->assertOk();

    $playlist->refresh();

    $this->assertModelMissing($track);
    $this->assertTrue($playlist->first()->doesntExist());
    $this->assertTrue($playlist->last()->doesntExist());
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

    $response = delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

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

    $response = delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $first]));

    $response->assertOk();

    $playlist->refresh();
    $second->refresh();

    $this->assertModelMissing($first);
    $this->assertTrue($playlist->first()->is($second));
    $this->assertTrue($second->previous()->doesntExist());
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

    $response = delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $last]));

    $response->assertOk();

    $playlist->refresh();
    $previous->refresh();

    $this->assertModelMissing($last);
    $this->assertTrue($playlist->last()->is($previous));
    $this->assertTrue($previous->next()->doesntExist());
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

    $response = delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $second]));

    $response->assertOk();

    $playlist->refresh();
    $first->refresh();
    $third->refresh();

    $this->assertModelMissing($second);

    $this->assertTrue($playlist->first()->is($first));
    $this->assertTrue($playlist->last()->is($third));

    $this->assertTrue($first->previous()->doesntExist());
    $this->assertTrue($first->next()->is($third));

    $this->assertTrue($third->previous()->is($first));
    $this->assertTrue($third->next()->doesntExist());
});
