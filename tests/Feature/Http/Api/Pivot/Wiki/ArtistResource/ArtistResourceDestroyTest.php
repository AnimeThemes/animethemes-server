<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\ArtistResource;

use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\ArtistResource;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ArtistResourceDestroyTest.
 */
class ArtistResourceDestroyTest extends TestCase
{
    use WithoutEvents;

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

        $user = User::factory()->withPermissions(['delete artist', 'delete external resource'])->createOne();

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

        $user = User::factory()->withPermissions(['delete artist', 'delete external resource'])->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artistresource.destroy', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource]));

        $response->assertOk();
        static::assertModelMissing($artistResource);
    }
}
