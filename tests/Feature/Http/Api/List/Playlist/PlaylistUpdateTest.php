<?php

declare(strict_types=1);

use App\Constants\Config\ValidationConstants;
use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Rules\ModerationService;
use App\Events\List\Playlist\PlaylistCreated;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('protected', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);

    $playlist = Playlist::factory()->createOne();

    $visibility = Arr::random(PlaylistVisibility::cases());

    $parameters = array_merge(
        Playlist::factory()->raw(),
        [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden if missing permission', function () {
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

    $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

    $response->assertForbidden();
});

test('forbidden if not own playlist', function () {
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

    $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

    $response->assertForbidden();
});

test('forbidden if flag disabled', function () {
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

    $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
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

    $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

    $response->assertOk();
});

test('update permitted for bypass', function () {
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

    $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

    $response->assertOk();
});

test('updated if not flagged by open ai', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);
    Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

    Http::fake([
        'https://api.openai.com/v1/moderations' => Http::response([
            'results' => [
                0 => [
                    'flagged' => false,
                ],
            ],
        ]),
    ]);

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

    $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

    $response->assertOk();
});

test('updated if open ai fails', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);
    Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

    Http::fake([
        'https://api.openai.com/v1/moderations' => Http::response(status: 404),
    ]);

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

    $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

    $response->assertOk();
});

test('validation error when flagged by open ai', function () {
    Event::fakeExcept(PlaylistCreated::class);

    Feature::activate(AllowPlaylistManagement::class);
    Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

    Http::fake([
        'https://api.openai.com/v1/moderations' => Http::response([
            'results' => [
                0 => [
                    'flagged' => true,
                ],
            ],
        ]),
    ]);

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

    $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

    $response->assertJsonValidationErrors([
        Playlist::ATTRIBUTE_NAME,
    ]);
});
