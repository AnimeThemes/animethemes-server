<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\ArtistSong;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ArtistSongUpdateTest.
 */
class ArtistSongUpdateTest extends TestCase
{
    /**
     * The Artist Resource Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $artistSong = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->createOne();

        $parameters = ArtistSong::factory()->raw();

        $response = $this->put(route('api.artistsong.update', ['artist' => $artistSong->artist, 'song' => $artistSong->song] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Resource Update Endpoint shall forbid users without the update artist & update song permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $artistSong = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->createOne();

        $parameters = ArtistSong::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.artistsong.update', ['artist' => $artistSong->artist, 'song' => $artistSong->song] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Artist Resource Update Endpoint shall update an artist song.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $artistSong = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->createOne();

        $parameters = ArtistSong::factory()->raw();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::UPDATE->format(Artist::class),
                CrudPermission::UPDATE->format(Song::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.artistsong.update', ['artist' => $artistSong->artist, 'song' => $artistSong->song] + $parameters));

        $response->assertOk();
    }
}
