<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Artist;

use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ArtistForceDeleteTest.
 */
class ArtistForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Artist Force Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $artist = Artist::factory()->createOne();

        $response = $this->delete(route('api.artist.forceDelete', ['artist' => $artist]));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Force Destroy Endpoint shall force delete the artist.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $artist = Artist::factory()->createOne();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('admin')->createOne(),
            ['*']
        );

        $response = $this->delete(route('api.artist.forceDelete', ['artist' => $artist]));

        $response->assertOk();
        static::assertModelMissing($artist);
    }
}
