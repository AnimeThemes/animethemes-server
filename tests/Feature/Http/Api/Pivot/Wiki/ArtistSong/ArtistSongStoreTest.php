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
 * Class ArtistSongStoreTest.
 */
class ArtistSongStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Artist Song Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $artistSong = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->makeOne();

        $response = $this->post(route('api.artistsong.store', $artistSong->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Song Store Endpoint shall forbid users without the create artist & create song permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $artistSong = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistsong.store', $artistSong->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Artist Song Store Endpoint shall require artist and song fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(Artist::class),
                CrudPermission::CREATE()->format(Song::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistsong.store'));

        $response->assertJsonValidationErrors([
            ArtistSong::ATTRIBUTE_ARTIST,
            ArtistSong::ATTRIBUTE_SONG,
        ]);
    }

    /**
     * The Artist Song Store Endpoint shall create an artist song.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = array_merge(
            ArtistSong::factory()->raw(),
            [ArtistSong::ATTRIBUTE_ARTIST => Artist::factory()->createOne()->getKey()],
            [ArtistSong::ATTRIBUTE_SONG => Song::factory()->createOne()->getKey()],
        );

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(Artist::class),
                CrudPermission::CREATE()->format(Song::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistsong.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(ArtistSong::TABLE, 1);
    }
}
