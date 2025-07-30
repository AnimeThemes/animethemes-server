<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Events\List\Playlist\PlaylistCreated;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Pivots\List\PlaylistImage;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('protected', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $playlist = Playlist::factory()->createOne();
    $image = Image::factory()->createOne();

    $response = post(route('api.playlistimage.store', ['playlist' => $playlist, 'image' => $image]));

    $response->assertUnauthorized();
});

test('forbidden if missing permission', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $playlist = Playlist::factory()->createOne();
    $image = Image::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.playlistimage.store', ['playlist' => $playlist, 'image' => $image]));

    $response->assertForbidden();
});

test('forbidden if flag disabled', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::deactivate(AllowPlaylistManagement::class);

    $playlist = Playlist::factory()->createOne();
    $image = Image::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(Playlist::class),
            CrudPermission::CREATE->format(Image::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.playlistimage.store', ['playlist' => $playlist, 'image' => $image]));

    $response->assertForbidden();
});

test('create', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $image = Image::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(Playlist::class),
            CrudPermission::CREATE->format(Image::class)
        )
        ->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.playlistimage.store', ['playlist' => $playlist, 'image' => $image]));

    $response->assertCreated();
    $this->assertDatabaseCount(PlaylistImage::class, 1);
});

test('create permitted for bypass', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class, fake()->boolean());

    $image = Image::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(Playlist::class),
            CrudPermission::CREATE->format(Image::class),
            SpecialPermission::BYPASS_FEATURE_FLAGS->value
        )
        ->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.playlistimage.store', ['playlist' => $playlist, 'image' => $image]));

    $response->assertCreated();
});
