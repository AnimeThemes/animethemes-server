<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\ArtistSong;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ArtistSongDestroyTest.
 */
class ArtistSongDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Artist Song Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $artistSong = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->createOne();

        $response = $this->delete(route('api.artistsong.destroy', ['artist' => $artistSong->artist, 'song' => $artistSong->song]));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Song Destroy Endpoint shall forbid users without the delete artist & delete song permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $artistSong = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artistsong.destroy', ['artist' => $artistSong->artist, 'song' => $artistSong->song]));

        $response->assertForbidden();
    }

    /**
     * The Artist Song Destroy Endpoint shall return an error if the artist song does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $artist = Artist::factory()->createOne();
        $song = Song::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE()->format(Artist::class),
                CrudPermission::DELETE()->format(Song::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artistsong.destroy', ['artist' => $artist, 'song' => $song]));

        $response->assertNotFound();
    }

    /**
     * The Artist Song Destroy Endpoint shall delete the artist song.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $artistSong = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE()->format(Artist::class),
                CrudPermission::DELETE()->format(Song::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artistsong.destroy', ['artist' => $artistSong->artist, 'song' => $artistSong->song]));

        $response->assertOk();
        static::assertModelMissing($artistSong);
    }
}
