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
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

pest()->use(WithFaker::class);

test('protected', function (): void {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class);

    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    $response = delete(route('api.playlist.track.destroy', ['playlist' => $track->playlist, 'track' => $track]));

    $response->assertUnauthorized();
});

test('forbidden if missing permission', function (): void {
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

test('forbidden if not own playlist', function (): void {
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

test('forbidden if flag disabled', function (): void {
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

test('scoped', function (): void {
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

test('deleted', function (): void {
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
    expect($playlist->first()->doesntExist())->toBeTrue();
    expect($playlist->last()->doesntExist())->toBeTrue();
});

test('destroy permitted for bypass', function (): void {
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

test('destroy first', function (): void {
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
    expect($playlist->first()->is($second))->toBeTrue();
    expect($second->previous()->doesntExist())->toBeTrue();
});

test('destroy last', function (): void {
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
    expect($playlist->last()->is($previous))->toBeTrue();
    expect($previous->next()->doesntExist())->toBeTrue();
});

test('destroy second', function (): void {
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

    expect($playlist->first()->is($first))->toBeTrue();
    expect($playlist->last()->is($third))->toBeTrue();

    expect($first->previous()->doesntExist())->toBeTrue();
    expect($first->next()->is($third))->toBeTrue();

    expect($third->previous()->is($first))->toBeTrue();
    expect($third->next()->doesntExist())->toBeTrue();
});
