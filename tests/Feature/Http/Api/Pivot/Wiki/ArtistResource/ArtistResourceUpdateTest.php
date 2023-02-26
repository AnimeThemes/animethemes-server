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
 * Class ArtistResourceUpdateTest.
 */
class ArtistResourceUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Artist Resource Update Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
            ->createOne();

        $parameters = ArtistResource::factory()->raw();

        $response = $this->put(route('api.artistresource.update', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Resource Update Endpoint shall forbid users without the update artist & update resource permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
            ->createOne();

        $parameters = ArtistResource::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.artistresource.update', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Artist Resource Update Endpoint shall update an artist resource.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
            ->createOne();

        $parameters = ArtistResource::factory()->raw();

        $user = User::factory()->withPermissions(['update artist', 'update external resource'])->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.artistresource.update', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource] + $parameters));

        $response->assertOk();
    }
}
