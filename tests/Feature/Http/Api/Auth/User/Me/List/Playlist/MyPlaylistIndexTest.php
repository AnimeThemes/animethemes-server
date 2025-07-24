<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Auth\User\Me\List\Playlist;

use App\Enums\Auth\CrudPermission;
use App\Http\Api\Query\Query;
use App\Http\Resources\List\Collection\PlaylistCollection;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MyPlaylistIndexTest extends TestCase
{
    use WithFaker;

    /**
     * The My Playlist Index Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $response = $this->get(route('api.me.playlist.index'));

        $response->assertUnauthorized();
    }

    /**
     * The My Playlist Index Endpoint shall forbid users without the view playlist permission.
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

        $user = User::factory()->withPermissions(CrudPermission::VIEW->format(Playlist::class))->createOne();

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
                    new PlaylistCollection($playlists, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
