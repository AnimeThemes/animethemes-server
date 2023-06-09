<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Events\List\Playlist\PlaylistCreated;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistForceDeleteTest.
 */
class PlaylistForceDeleteTest extends TestCase
{
    use WithFaker;

    /**
     * The Playlist Force Delete Endpoint shall require authorization.
     *
     * @return void
     */
    public function testAuthorized(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

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
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $playlist = Playlist::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.forceDelete', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Force Delete Endpoint shall forbid users from force deleting playlists
     * if the Allow Playlist Management feature is inactive.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::deactivate(AllowPlaylistManagement::class);

        $playlist = Playlist::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Playlist::class))->createOne();

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
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $playlist = Playlist::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.forceDelete', ['playlist' => $playlist]));

        $response->assertOk();
        static::assertModelMissing($playlist);
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to force delete playlists
     * even if the Allow Playlist Management feature is inactive.
     *
     * @return void
     */
    public function testDeletePermittedForBypass(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                ExtendedCrudPermission::FORCE_DELETE->format(Playlist::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS->value
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
