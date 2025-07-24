<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\ArtistSong;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArtistSongStoreTest extends TestCase
{
    /**
     * The Artist Song Store Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $artist = Artist::factory()->createOne();
        $song = Song::factory()->createOne();

        $parameters = ArtistSong::factory()->raw();

        $response = $this->post(route('api.artistsong.store', ['artist' => $artist, 'song' => $song] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Song Store Endpoint shall forbid users without the create artist & create song permissions.
     */
    public function testForbidden(): void
    {
        $artist = Artist::factory()->createOne();
        $song = Song::factory()->createOne();

        $parameters = ArtistSong::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistsong.store', ['artist' => $artist, 'song' => $song] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Artist Song Store Endpoint shall create an artist song.
     */
    public function testCreate(): void
    {
        $artist = Artist::factory()->createOne();
        $song = Song::factory()->createOne();

        $parameters = ArtistSong::factory()->raw();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE->format(Artist::class),
                CrudPermission::CREATE->format(Song::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistsong.store', ['artist' => $artist, 'song' => $song] + $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(ArtistSong::class, 1);
    }
}
