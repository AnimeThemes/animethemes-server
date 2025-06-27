<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Enums\Auth\CrudPermission;
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
 * Class PlaylistDestroyTest.
 */
class PlaylistDestroyTest extends TestCase
{
    use WithFaker;

    /**
     * The Playlist Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

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
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

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
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.destroy', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Destroy Endpoint shall forbid users from destroying playlists
     * if the Allow Playlist Management feature is inactive.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::deactivate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Playlist::class))->createOne();

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
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->trashed()
            ->for($user)
            ->createOne();

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
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Playlist::class))->createOne();

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
     * even if the Allow Playlist Management feature is inactive.
     *
     * @return void
     */
    public function testDestroyPermittedForBypass(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Playlist::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS->value
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
