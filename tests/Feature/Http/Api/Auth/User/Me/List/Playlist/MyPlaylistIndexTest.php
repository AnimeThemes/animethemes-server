<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Auth\User\Me\List\Playlist;

use App\Http\Api\Query\Auth\User\Me\List\Playlist\MyPlaylistReadQuery;
use App\Http\Resources\List\Collection\PlaylistCollection;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class MyPlaylistIndexTest.
 */
class MyPlaylistIndexTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The My Playlist Index Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $response = $this->get(route('api.me.playlist.index'));

        $response->assertUnauthorized();
    }

    /**
     * The My Playlist Index Endpoint shall forbid users without the view playlist permission.
     *
     * @return void
     */
    public function testForbiddenIfMissingPermission(): void
    {
        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.me.playlist.index'));

        $response->assertForbidden();
    }

    /**
     * The My Playlist Index Endpoint shall return playlists owned by the user.
     *
     * @return void
     */
    public function testOnlySeesOwnedPlaylists(): void
    {
        Playlist::factory()
            ->for(User::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        Playlist::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $user = User::factory()->withPermission('view playlist')->createOne();

        $playlistCount = $this->faker->randomDigitNotNull();

        $playlists = Playlist::factory()
            ->for($user)
            ->count($playlistCount)
            ->create();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.me.playlist.index'));

        $response->assertJsonCount($playlistCount, PlaylistCollection::$wrap);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistCollection($playlists, new MyPlaylistReadQuery($user)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
