<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Constants\Config\FlagConstants;
use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistDestroyTest.
 */
class PlaylistDestroyTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Playlist Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $playlist = Playlist::factory()->createOne();

        $response = $this->delete(route('api.playlist.destroy', ['playlist' => $playlist]));

        $response->assertUnauthorized();
    }

    /**
     * The Playlist Destroy Endpoint shall forbid users without the delete playlist permission.
     *
     * @return void
     */
    public function testForbiddenIfMissingPermission(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $playlist = Playlist::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.destroy', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Destroy Endpoint shall forbid users from deleting the playlist if they don't own it.
     *
     * @return void
     */
    public function testForbiddenIfNotOwnPlaylist(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE()->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.destroy', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Destroy Endpoint shall forbid users from destroying playlists
     * if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, false);

        $user = User::factory()->withPermissions(CrudPermission::DELETE()->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.destroy', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Destroy Endpoint shall forbid users from updating a playlist that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::DELETE()->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $playlist->delete();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.destroy', ['playlist' => $playlist]));

        $response->assertNotFound();
    }

    /**
     * The Playlist Destroy Endpoint shall delete the playlist.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::DELETE()->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.destroy', ['playlist' => $playlist]));

        $response->assertOk();
        static::assertSoftDeleted($playlist);
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to destroy playlists
     * even if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testDestroyPermittedForBypass(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE()->format(Playlist::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS
            )
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.destroy', ['playlist' => $playlist]));

        $response->assertOk();
    }
}
