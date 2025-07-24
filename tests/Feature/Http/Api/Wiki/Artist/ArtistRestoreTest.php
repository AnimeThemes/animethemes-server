<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Artist;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArtistRestoreTest extends TestCase
{
    /**
     * The Artist Restore Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $artist = Artist::factory()->trashed()->createOne();

        $response = $this->patch(route('api.artist.restore', ['artist' => $artist]));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Restore Endpoint shall forbid users without the restore artist permission.
     */
    public function testForbidden(): void
    {
        $artist = Artist::factory()->trashed()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.artist.restore', ['artist' => $artist]));

        $response->assertForbidden();
    }

    /**
     * The Artist Restore Endpoint shall forbid users from restoring an artist that isn't trashed.
     */
    public function testTrashed(): void
    {
        $artist = Artist::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.artist.restore', ['artist' => $artist]));

        $response->assertForbidden();
    }

    /**
     * The Artist Restore Endpoint shall restore the artist.
     */
    public function testRestored(): void
    {
        $artist = Artist::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.artist.restore', ['artist' => $artist]));

        $response->assertOk();
        static::assertNotSoftDeleted($artist);
    }
}
