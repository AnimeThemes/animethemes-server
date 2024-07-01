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

/**
 * Class ArtistResourceDestroyTest.
 */
class ArtistResourceDestroyTest extends TestCase
{
    /**
     * The Artist Resource Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->delete(route('api.artistresource.destroy', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource]));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Resource Destroy Endpoint shall forbid users without the delete artist & delete resource permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artistresource.destroy', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource]));

        $response->assertForbidden();
    }

    /**
     * The Artist Resource Destroy Endpoint shall return an error if the artist resource does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $artist = Artist::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Artist::class),
                CrudPermission::DELETE->format(ExternalResource::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artistresource.destroy', ['artist' => $artist, 'resource' => $resource]));

        $response->assertNotFound();
    }

    /**
     * The Artist Resource Destroy Endpoint shall delete the artist resource.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
            ->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Artist::class),
                CrudPermission::DELETE->format(ExternalResource::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artistresource.destroy', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource]));

        $response->assertOk();
        static::assertModelMissing($artistResource);
    }
}
