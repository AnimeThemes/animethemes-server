<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Constants\Config\FlagConstants;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistRestoreTest.
 */
class PlaylistRestoreTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Playlist Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $playlist = Playlist::factory()->createOne();

        $response = $this->patch(route('api.playlist.restore', ['playlist' => $playlist]));

        $response->assertUnauthorized();
    }

    /**
     * The Playlist Restore Endpoint shall forbid users without the restore playlist permission.
     *
     * @return void
     */
    public function testForbiddenIfMissingPermission(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $playlist = Playlist::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.playlist.restore', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Restore Endpoint shall forbid users from restoring the playlist if they don't own it.
     *
     * @return void
     */
    public function testForbiddenIfNotOwnPlaylist(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE()->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.playlist.restore', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Restore Endpoint shall forbid users from restoring playlists
     * if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, false);

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE()->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $playlist->delete();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.playlist.restore', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Restore Endpoint shall forbid users from restoring a playlist that isn't trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE()->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.playlist.restore', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Restore Endpoint shall restore the playlist.
     *
     * @return void
     */
    public function testRestored(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE()->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $playlist->delete();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.playlist.restore', ['playlist' => $playlist]));

        $response->assertOk();
        static::assertNotSoftDeleted($playlist);
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to restore playlists
     * even if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testCreatePermittedForBypass(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                ExtendedCrudPermission::RESTORE()->format(Playlist::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS
            )
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $playlist->delete();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.playlist.restore', ['playlist' => $playlist]));

        $response->assertOk();
        static::assertNotSoftDeleted($playlist);
    }
}
