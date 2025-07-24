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

class ArtistResourceUpdateTest extends TestCase
{
    /**
     * The Artist Resource Update Endpoint shall be protected by sanctum.
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
     */
    public function testUpdate(): void
    {
        $artistResource = ArtistResource::factory()
            ->for(Artist::factory())
            ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
            ->createOne();

        $parameters = ArtistResource::factory()->raw();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::UPDATE->format(Artist::class),
                CrudPermission::UPDATE->format(ExternalResource::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.artistresource.update', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource] + $parameters));

        $response->assertOk();
    }
}
