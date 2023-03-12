<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Constants\Config\FlagConstants;
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
 * Class PlaylistUpdateTest.
 */
class PlaylistUpdateTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Playlist Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $playlist = Playlist::factory()->createOne();

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description],
        );

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Playlist Update Endpoint shall forbid users without the update playlist permission.
     *
     * @return void
     */
    public function testForbiddenIfMissingPermission(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $playlist = Playlist::factory()->createOne();

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description],
        );

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Playlist Update Endpoint shall forbid users from updating the playlist if they don't own it.
     *
     * @return void
     */
    public function testForbiddenIfNotOwnPlaylist(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne();

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description],
        );

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Playlist Update Endpoint shall forbid users from updating playlists
     * if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, false);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description,
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Playlist Update Endpoint shall forbid users from updating a playlist that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $playlist->delete();

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description,
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Playlist Update Endpoint shall update a playlist.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description,
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertOk();
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to update playlists
     * even if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testUpdatePermittedForBypass(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                CrudPermission::UPDATE()->format(Playlist::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS
            )
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $parameters = array_merge(
            Playlist::factory()->raw(),
            [
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomInstance()->description,
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.update', ['playlist' => $playlist] + $parameters));

        $response->assertOk();
    }
}
