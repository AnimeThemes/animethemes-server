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

use function Pest\Laravel\delete;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('protected', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $playlistImage = PlaylistImage::factory()
        ->for(Playlist::factory())
        ->for(Image::factory())
        ->createOne();

    $response = delete(route('api.playlistimage.destroy', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

    $response->assertUnauthorized();
});

test('forbidden if missing permission', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $playlistImage = PlaylistImage::factory()
        ->for(Playlist::factory())
        ->for(Image::factory())
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.playlistimage.destroy', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

    $response->assertForbidden();
});

test('forbidden if not own playlist', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $playlistImage = PlaylistImage::factory()
        ->for(Playlist::factory()->for(User::factory()))
        ->for(Image::factory())
        ->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Playlist::class),
            CrudPermission::DELETE->format(Image::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.playlistimage.destroy', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

    $response->assertForbidden();
});

test('forbidden if flag disabled', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::deactivate(AllowPlaylistManagement::class);

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Playlist::class),
            CrudPermission::DELETE->format(Image::class)
        )
        ->createOne();

    $playlistImage = PlaylistImage::factory()
        ->for(Playlist::factory()->for($user))
        ->for(Image::factory())
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.playlistimage.destroy', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

    $response->assertForbidden();
});

test('not found', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Playlist::class),
            CrudPermission::DELETE->format(Image::class)
        )
        ->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();
    $image = Image::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.playlistimage.destroy', ['playlist' => $playlist, 'image' => $image]));

    $response->assertNotFound();
});

test('deleted', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Playlist::class),
            CrudPermission::DELETE->format(Image::class)
        )
        ->createOne();

    $playlistImage = PlaylistImage::factory()
        ->for(Playlist::factory()->for($user))
        ->for(Image::factory())
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.playlistimage.destroy', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

    $response->assertOk();
    $this->assertModelMissing($playlistImage);
});

test('destroy permitted for bypass', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class, fake()->boolean());

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Playlist::class),
            CrudPermission::DELETE->format(Image::class),
            SpecialPermission::BYPASS_FEATURE_FLAGS->value
        )
        ->createOne();

    $playlistImage = PlaylistImage::factory()
        ->for(Playlist::factory()->for($user))
        ->for(Image::factory())
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.playlistimage.destroy', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

    $response->assertOk();
});
