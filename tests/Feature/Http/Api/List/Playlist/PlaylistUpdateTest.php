<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Events\List\Playlist\PlaylistCreated;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

uses(WithFaker::class);

test('protected', function (): void {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $playlist = Playlist::factory()->createOne();

    $visibility = Arr::random(PlaylistVisibility::cases());

    $parameters = array_merge(
        Playlist::factory()->raw(),
        [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $response = put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden if missing permission', function (): void {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $playlist = Playlist::factory()->createOne();

    $visibility = Arr::random(PlaylistVisibility::cases());

    $parameters = array_merge(
        Playlist::factory()->raw(),
        [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

    $response->assertForbidden();
});

test('forbidden if not own playlist', function (): void {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->createOne();

    $visibility = Arr::random(PlaylistVisibility::cases());

    $parameters = array_merge(
        Playlist::factory()->raw(),
        [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Playlist::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

    $response->assertForbidden();
});

test('forbidden if flag disabled', function (): void {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::deactivate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Playlist::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    $visibility = Arr::random(PlaylistVisibility::cases());

    $parameters = array_merge(
        Playlist::factory()->raw(),
        [
            Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize(),
        ],
    );

    Sanctum::actingAs($user);

    $response = put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

    $response->assertForbidden();
});

test('update', function (): void {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Playlist::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    $visibility = Arr::random(PlaylistVisibility::cases());

    $parameters = array_merge(
        Playlist::factory()->raw(),
        [
            Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize(),
        ],
    );

    Sanctum::actingAs($user);

    $response = put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

    $response->assertOk();
});

test('update permitted for bypass', function (): void {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class, fake()->boolean());

    $user = User::factory()
        ->withPermissions(
            CrudPermission::UPDATE->format(Playlist::class),
            SpecialPermission::BYPASS_FEATURE_FLAGS->value
        )
        ->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    $visibility = Arr::random(PlaylistVisibility::cases());

    $parameters = array_merge(
        Playlist::factory()->raw(),
        [
            Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize(),
        ],
    );

    Sanctum::actingAs($user);

    $response = put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

    $response->assertOk();
});
