<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistForceDeleteTest.
 */
class PlaylistForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Playlist Force Delete Endpoint shall require authorization.
     *
     * @return void
     */
    public function testAuthorized(): void
    {
        $playlist = Playlist::factory()->createOne();

        $response = $this->delete(route('api.playlist.forceDelete', ['playlist' => $playlist]));

        $response->assertUnauthorized();
    }

    /**
     * The Playlist Force Delete Endpoint shall forbid users without the force delete playlist permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $playlist = Playlist::factory()->createOne();

        $user = User::factory()->createOne();

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
        $playlist = Playlist::factory()->createOne();

        $user = User::factory()->withPermission('force delete playlist')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.forceDelete', ['playlist' => $playlist]));

        $response->assertOk();
        static::assertModelMissing($playlist);
    }
}
