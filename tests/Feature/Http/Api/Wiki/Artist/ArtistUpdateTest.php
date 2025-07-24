<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Artist;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArtistUpdateTest extends TestCase
{
    /**
     * The Artist Update Endpoint shall be protected by sanctum.
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
     */
    public function testTrashed(): void
    {
        $artist = Artist::factory()->trashed()->createOne();

        $parameters = Artist::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.artist.update', ['artist' => $artist] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Artist Update Endpoint shall update an artist.
     */
    public function testUpdate(): void
    {
        $artist = Artist::factory()->createOne();

        $parameters = Artist::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.artist.update', ['artist' => $artist] + $parameters));

        $response->assertOk();
    }
}
