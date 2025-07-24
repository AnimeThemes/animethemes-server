<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\ArtistResource;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\ArtistResource;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArtistResourceStoreTest extends TestCase
{
    /**
     * The Artist Resource Store Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $artist = Artist::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $parameters = ArtistResource::factory()->raw();

        $response = $this->post(route('api.artistresource.store', ['artist' => $artist, 'resource' => $resource] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Resource Store Endpoint shall forbid users without the create artist & create resource permissions.
     */
    public function testForbidden(): void
    {
        $artist = Artist::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $parameters = ArtistResource::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistresource.store', ['artist' => $artist, 'resource' => $resource] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Artist Resource Store Endpoint shall create an artist resource.
     */
    public function testCreate(): void
    {
        $artist = Artist::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $parameters = ArtistResource::factory()->raw();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE->format(Artist::class),
                CrudPermission::CREATE->format(ExternalResource::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistresource.store', ['artist' => $artist, 'resource' => $resource] + $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(ArtistResource::class, 1);
    }
}
