<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Artist;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ArtistForceDeleteTest.
 */
class ArtistForceDeleteTest extends TestCase
{
    /**
     * The Artist Force Delete Endpoint shall be protected by sanctum.
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
     * The Artist Force Delete Endpoint shall forbid users without the force delete artist permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $artist = Artist::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artist.forceDelete', ['artist' => $artist]));

        $response->assertForbidden();
    }

    /**
     * The Artist Force Delete Endpoint shall force delete the artist.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $artist = Artist::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artist.forceDelete', ['artist' => $artist]));

        $response->assertOk();
        static::assertModelMissing($artist);
    }
}
