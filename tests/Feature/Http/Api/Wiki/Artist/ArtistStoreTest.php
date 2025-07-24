<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Artist;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArtistStoreTest extends TestCase
{
    /**
     * The Artist Store Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $artist = Artist::factory()->makeOne();

        $response = $this->post(route('api.artist.store', $artist->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Store Endpoint shall forbid users without the create artist permission.
     */
    public function testForbidden(): void
    {
        $artist = Artist::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artist.store', $artist->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Artist Store Endpoint shall require name & slug fields.
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artist.store'));

        $response->assertJsonValidationErrors([
            Artist::ATTRIBUTE_NAME,
            Artist::ATTRIBUTE_SLUG,
        ]);
    }

    /**
     * The Artist Store Endpoint shall create an artist.
     */
    public function testCreate(): void
    {
        $parameters = Artist::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Artist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artist.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Artist::class, 1);
    }
}
