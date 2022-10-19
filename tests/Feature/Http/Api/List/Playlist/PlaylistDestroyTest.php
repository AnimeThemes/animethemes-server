<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistDestroyTest.
 */
class PlaylistDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Playlist Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
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
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne();

        $user = User::factory()->withPermission('delete playlist')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.destroy', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Destroy Endpoint shall delete the playlist.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $user = User::factory()->withPermission('delete playlist')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.destroy', ['playlist' => $playlist]));

        $response->assertOk();
        static::assertSoftDeleted($playlist);
    }
}
