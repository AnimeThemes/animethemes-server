<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Artist;

use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ArtistRestoreTest.
 */
class ArtistRestoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Artist Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $artist = Artist::factory()->createOne();

        $artist->delete();

        $response = $this->patch(route('api.artist.restore', ['artist' => $artist]));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Restore Endpoint shall restore the artist.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $artist = Artist::factory()->createOne();

        $artist->delete();

        $user = User::factory()->createOne();

        $user->givePermissionTo('restore artist');

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.artist.restore', ['artist' => $artist]));

        $response->assertOk();
        static::assertNotSoftDeleted($artist);
    }
}
