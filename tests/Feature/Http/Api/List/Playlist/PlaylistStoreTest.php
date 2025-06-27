<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Constants\Config\PlaylistConstants;
use App\Constants\Config\ValidationConstants;
use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Rules\ModerationService;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistStoreTest.
 */
class PlaylistStoreTest extends TestCase
{
    use WithFaker;

    /**
     * The Playlist Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Feature::activate(AllowPlaylistManagement::class);

        $playlist = Playlist::factory()->makeOne();

        $response = $this->post(route('api.playlist.store', $playlist->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Playlist Store Endpoint shall forbid users without the create playlist permission.
     *
     * @return void
     */
    public function testForbiddenIfMissingPermission(): void
    {
        Feature::activate(AllowPlaylistManagement::class);

        $playlist = Playlist::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.store', $playlist->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Playlist Store Endpoint shall forbid users from creating playlists
     * if the Allow Playlist Management feature is inactive.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Feature::deactivate(AllowPlaylistManagement::class);

        $visibility = Arr::random(PlaylistVisibility::cases());

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
        );

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.store', $parameters));

        $response->assertForbidden();
    }

    /**
     * The Playlist Store Endpoint shall require name & visibility fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.store'));

        $response->assertJsonValidationErrors([
            Playlist::ATTRIBUTE_NAME,
            Playlist::ATTRIBUTE_VISIBILITY,
        ]);
    }

    /**
     * The Playlist Store Endpoint shall create a playlist.
     *
     * @return void
     */
    public function testCreate(): void
    {
        Feature::activate(AllowPlaylistManagement::class);

        $visibility = Arr::random(PlaylistVisibility::cases());

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => $visibility->localize()],
        );

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Playlist::class, 1);
        static::assertDatabaseHas(Playlist::class, [Playlist::ATTRIBUTE_USER => $user->getKey()]);
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to create playlists
     * even if the Allow Playlist Management feature is inactive.
     *
     * @return void
     */
    public function testCreatePermittedForBypass(): void
    {
        Feature::activate(AllowPlaylistManagement::class, $this->faker->boolean());

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

        $response = $this->post(route('api.playlist.store', $parameters));

        $response->assertCreated();
    }

    /**
     * The Playlist Store Endpoint shall forbid users from creating playlists that exceed the user playlist limit.
     *
     * @return void
     */
    public function testMaxTrackLimit(): void
    {
        $playlistLimit = $this->faker->randomDigitNotNull();

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

        $response = $this->post(route('api.playlist.store', $parameters));

        $response->assertForbidden();
    }

    /**
     * The Playlist Store Endpoint shall permit users with bypass feature flag permission
     * to create playlists that exceed the user playlist limit.
     *
     * @return void
     */
    public function testMaxTrackLimitPermittedForBypass(): void
    {
        $playlistLimit = $this->faker->randomDigitNotNull();

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

        $response = $this->post(route('api.playlist.store', $parameters));

        $response->assertCreated();
    }

    /**
     * The Playlist Store Endpoint shall create a playlist if the name is not flagged by OpenAI.
     *
     * @return void
     */
    public function testCreatedIfNotFlaggedByOpenAi(): void
    {
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

        $response = $this->post(route('api.playlist.store', $parameters));

        $response->assertCreated();
    }

    /**
     * The Playlist Store Endpoint shall create a playlist if the moderation service returns some error.
     *
     * @return void
     */
    public function testCreatedIfOpenAiFails(): void
    {
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

        $response = $this->post(route('api.playlist.store', $parameters));

        $response->assertCreated();
    }

    /**
     * The Playlist Store Endpoint shall prohibit users from creating playlists with names flagged by OpenAI.
     *
     * @return void
     */
    public function testValidationErrorWhenFlaggedByOpenAi(): void
    {
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

        $response = $this->post(route('api.playlist.store', $parameters));

        $response->assertJsonValidationErrors([
            Playlist::ATTRIBUTE_NAME,
        ]);
    }
}
