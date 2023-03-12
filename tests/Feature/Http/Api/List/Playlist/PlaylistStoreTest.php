<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Constants\Config\FlagConstants;
use App\Constants\Config\PlaylistConstants;
use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistStoreTest.
 */
class PlaylistStoreTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Playlist Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $playlist = Playlist::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.store', $playlist->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Playlist Store Endpoint shall forbid users from creating playlists
     * if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, false);

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description],
        );

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(Playlist::class))->createOne();

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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(Playlist::class))->createOne();

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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description],
        );

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Playlist::TABLE, 1);
        static::assertDatabaseHas(Playlist::TABLE, [Playlist::ATTRIBUTE_USER => $user->getKey()]);
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to create playlists
     * even if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testCreatePermittedForBypass(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, $this->faker->boolean());

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description],
        );

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(Playlist::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS
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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description],
        );

        $user = User::factory()
            ->has(Playlist::factory()->count($playlistLimit))
            ->withPermissions(CrudPermission::CREATE()->format(Playlist::class))
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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description],
        );

        $user = User::factory()
            ->has(Playlist::factory()->count($playlistLimit))
            ->withPermissions(
                CrudPermission::CREATE()->format(Playlist::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.store', $parameters));

        $response->assertCreated();
    }
}
