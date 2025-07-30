<?php

declare(strict_types=1);

use App\Constants\Config\PlaylistConstants;
use App\Constants\Config\ValidationConstants;
use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Rules\ModerationService;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('protected', function () {
    Feature::activate(AllowPlaylistManagement::class);

    $playlist = Playlist::factory()->makeOne();

    $response = post(route('api.playlist.store', $playlist->toArray()));

    $response->assertUnauthorized();
});

test('forbidden if missing permission', function () {
    Feature::activate(AllowPlaylistManagement::class);

    $playlist = Playlist::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.playlist.store', $playlist->toArray()));

    $response->assertForbidden();
});

test('forbidden if flag disabled', function () {
    Feature::deactivate(AllowPlaylistManagement::class);

    $visibility = Arr::random(PlaylistVisibility::cases());

    $parameters = array_merge(
        Playlist::factory()->raw(),
        [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Playlist::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.playlist.store', $parameters));

    $response->assertForbidden();
});

test('required fields', function () {
    Feature::activate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Playlist::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.playlist.store'));

    $response->assertJsonValidationErrors([
        Playlist::ATTRIBUTE_NAME,
        Playlist::ATTRIBUTE_VISIBILITY,
    ]);
});

test('create', function () {
    Feature::activate(AllowPlaylistManagement::class);

    $visibility = Arr::random(PlaylistVisibility::cases());

    $parameters = array_merge(
        Playlist::factory()->raw(),
        [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Playlist::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.playlist.store', $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(Playlist::class, 1);
    $this->assertDatabaseHas(Playlist::class, [Playlist::ATTRIBUTE_USER => $user->getKey()]);
});

test('create permitted for bypass', function () {
    Feature::activate(AllowPlaylistManagement::class, fake()->boolean());

    $visibility = Arr::random(PlaylistVisibility::cases());

    $parameters = array_merge(
        Playlist::factory()->raw(),
        [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(Playlist::class),
            SpecialPermission::BYPASS_FEATURE_FLAGS->value
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.playlist.store', $parameters));

    $response->assertCreated();
});

test('max track limit', function () {
    $playlistLimit = fake()->randomDigitNotNull();

    Config::set(PlaylistConstants::MAX_PLAYLISTS_QUALIFIED, $playlistLimit);
    Feature::activate(AllowPlaylistManagement::class);

    $visibility = Arr::random(PlaylistVisibility::cases());

    $parameters = array_merge(
        Playlist::factory()->raw(),
        [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()
        ->has(Playlist::factory()->count($playlistLimit))
        ->withPermissions(CrudPermission::CREATE->format(Playlist::class))
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.playlist.store', $parameters));

    $response->assertForbidden();
});

test('max track limit permitted for bypass', function () {
    $playlistLimit = fake()->randomDigitNotNull();

    Config::set(PlaylistConstants::MAX_PLAYLISTS_QUALIFIED, $playlistLimit);
    Feature::activate(AllowPlaylistManagement::class);

    $visibility = Arr::random(PlaylistVisibility::cases());

    $parameters = array_merge(
        Playlist::factory()->raw(),
        [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()
        ->has(Playlist::factory()->count($playlistLimit))
        ->withPermissions(
            CrudPermission::CREATE->format(Playlist::class),
            SpecialPermission::BYPASS_FEATURE_FLAGS->value
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.playlist.store', $parameters));

    $response->assertCreated();
});

test('created if not flagged by open ai', function () {
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

    $visibility = Arr::random(PlaylistVisibility::cases());

    $parameters = array_merge(
        Playlist::factory()->raw(),
        [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Playlist::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.playlist.store', $parameters));

    $response->assertCreated();
});

test('created if open ai fails', function () {
    Feature::activate(AllowPlaylistManagement::class);
    Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

    Http::fake([
        'https://api.openai.com/v1/moderations' => Http::response(status: 404),
    ]);

    $visibility = Arr::random(PlaylistVisibility::cases());

    $parameters = array_merge(
        Playlist::factory()->raw(),
        [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Playlist::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.playlist.store', $parameters));

    $response->assertCreated();
});

test('validation error when flagged by open ai', function () {
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

    $visibility = Arr::random(PlaylistVisibility::cases());

    $parameters = array_merge(
        Playlist::factory()->raw(),
        [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Playlist::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.playlist.store', $parameters));

    $response->assertJsonValidationErrors([
        Playlist::ATTRIBUTE_NAME,
    ]);
});
