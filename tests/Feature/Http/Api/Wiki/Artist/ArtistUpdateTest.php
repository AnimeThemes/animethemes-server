<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Artist;

use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ArtistUpdateTest.
 */
class ArtistUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Artist Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $artist = Artist::factory()->createOne();

        $parameters = Artist::factory()->raw();

        $response = $this->put(route('api.artist.update', ['artist' => $artist] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Update Endpoint shall forbid users without the update artist permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $artist = Artist::factory()->createOne();

        $parameters = Artist::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.artist.update', ['artist' => $artist] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Artist Update Endpoint shall forbid users from updating an artist that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $artist = Artist::factory()->createOne();

        $artist->delete();

        $parameters = Artist::factory()->raw();

        $user = User::factory()->withPermission('update artist')->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.artist.update', ['artist' => $artist] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Artist Update Endpoint shall update an artist.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $artist = Artist::factory()->createOne();

        $parameters = Artist::factory()->raw();

        $user = User::factory()->withPermission('update artist')->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.artist.update', ['artist' => $artist] + $parameters));

        $response->assertOk();
    }
}
