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
 * Class ArtistResourceStoreTest.
 */
class ArtistResourceStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Artist Resource Store Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
            ->makeOne();

        $response = $this->post(route('api.artistresource.store', $artistResource->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Resource Store Endpoint shall forbid users without the create artist & create resource permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
            ->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistresource.store', $artistResource->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Artist Resource Store Endpoint shall require artist and resource fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(['create artist', 'create external resource'])->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistresource.store'));

        $response->assertJsonValidationErrors([
            ArtistResource::ATTRIBUTE_ARTIST,
            ArtistResource::ATTRIBUTE_RESOURCE,
        ]);
    }

    /**
     * The Artist Resource Store Endpoint shall create an artist resource.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = array_merge(
            ArtistResource::factory()->raw(),
            [ArtistResource::ATTRIBUTE_ARTIST => Artist::factory()->createOne()->getKey()],
            [ArtistResource::ATTRIBUTE_RESOURCE => ExternalResource::factory()->createOne()->getKey()],
        );

        $user = User::factory()->withPermissions(['create artist', 'create external resource'])->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistresource.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(ArtistResource::TABLE, 1);
    }
}
