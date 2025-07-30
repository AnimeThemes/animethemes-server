<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Events\List\Playlist\PlaylistCreated;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('protected', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $playlist = Playlist::factory()->createOne();

    $response = delete(route('api.playlist.destroy', ['playlist' => $playlist]));

    $response->assertUnauthorized();
});

test('forbidden if missing permission', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $playlist = Playlist::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.playlist.destroy', ['playlist' => $playlist]));

    $response->assertForbidden();
});

test('forbidden if not own playlist', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Playlist::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.playlist.destroy', ['playlist' => $playlist]));

    $response->assertForbidden();
});

test('forbidden if flag disabled', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::deactivate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Playlist::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.playlist.destroy', ['playlist' => $playlist]));

    $response->assertForbidden();
});

test('deleted', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Playlist::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.playlist.destroy', ['playlist' => $playlist]));

    $response->assertOk();
    $this->assertModelMissing($playlist);
});

test('destroy permitted for bypass', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class, fake()->boolean());

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Playlist::class),
            SpecialPermission::BYPASS_FEATURE_FLAGS->value
        )
        ->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.playlist.destroy', ['playlist' => $playlist]));

    $response->assertOk();
});
