<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistRestoreTest.
 */
class PlaylistRestoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Playlist Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
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
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne();

        $user = User::factory()->withPermission('restore playlist')->createOne();

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
        $user = User::factory()->withPermission('restore playlist')->createOne();

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
        $user = User::factory()->withPermission('restore playlist')->createOne();

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
