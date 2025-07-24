<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Artist;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArtistDestroyTest extends TestCase
{
    /**
     * The Artist Destroy Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $artist = Artist::factory()->createOne();

        $response = $this->delete(route('api.artist.destroy', ['artist' => $artist]));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Destroy Endpoint shall forbid users without the delete artist permission.
     */
    public function testForbidden(): void
    {
        $artist = Artist::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artist.destroy', ['artist' => $artist]));

        $response->assertForbidden();
    }

    /**
     * The Artist Destroy Endpoint shall forbid users from updating an artist that is trashed.
     */
    public function testTrashed(): void
    {
        $artist = Artist::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artist.destroy', ['artist' => $artist]));

        $response->assertNotFound();
    }

    /**
     * The Artist Destroy Endpoint shall delete the artist.
     */
    public function testDeleted(): void
    {
        $artist = Artist::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artist.destroy', ['artist' => $artist]));

        $response->assertOk();
        static::assertSoftDeleted($artist);
    }
}
