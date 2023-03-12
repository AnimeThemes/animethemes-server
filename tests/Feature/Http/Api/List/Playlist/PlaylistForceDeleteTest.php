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
 * Class PlaylistForceDeleteTest.
 */
class PlaylistForceDeleteTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Playlist Force Delete Endpoint shall require authorization.
     *
     * @return void
     */
    public function testAuthorized(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $playlist = Playlist::factory()->createOne();

        $response = $this->delete(route('api.playlist.forceDelete', ['playlist' => $playlist]));

        $response->assertUnauthorized();
    }

    /**
     * The Playlist Force Delete Endpoint shall forbid users without the force delete playlist permission.
     *
     * @return void
     */
    public function testForbiddenIfMissingPermission(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $playlist = Playlist::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.forceDelete', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Force Delete Endpoint shall forbid users from force deleting playlists
     * if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, false);

        $playlist = Playlist::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE()->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.forceDelete', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Force Delete Endpoint shall force delete the playlist.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $playlist = Playlist::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE()->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.forceDelete', ['playlist' => $playlist]));

        $response->assertOk();
        static::assertModelMissing($playlist);
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to force delete playlists
     * even if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testDeletePermittedForBypass(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                ExtendedCrudPermission::FORCE_DELETE()->format(Playlist::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS
            )
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.forceDelete', ['playlist' => $playlist]));

        $response->assertOk();
    }
}
