<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\StudioResource;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudioResourceDestroyTest extends TestCase
{
    /**
     * The Studio Resource Destroy Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $studioResource = StudioResource::factory()
            ->for(Studio::factory())
            ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
            ->createOne();

        $response = $this->delete(route('api.studioresource.destroy', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource]));

        $response->assertUnauthorized();
    }

    /**
     * The Studio Resource Destroy Endpoint shall forbid users without the delete studio & delete resource permissions.
     */
    public function testForbidden(): void
    {
        $studioResource = StudioResource::factory()
            ->for(Studio::factory())
            ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.studioresource.destroy', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource]));

        $response->assertForbidden();
    }

    /**
     * The Studio Resource Destroy Endpoint shall return an error if the studio resource does not exist.
     */
    public function testNotFound(): void
    {
        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Studio::class),
                CrudPermission::DELETE->format(ExternalResource::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.studioresource.destroy', ['studio' => $studio, 'resource' => $resource]));

        $response->assertNotFound();
    }

    /**
     * The Studio Resource Destroy Endpoint shall delete the studio resource.
     */
    public function testDeleted(): void
    {
        $studioResource = StudioResource::factory()
            ->for(Studio::factory())
            ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
            ->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Studio::class),
                CrudPermission::DELETE->format(ExternalResource::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.studioresource.destroy', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource]));

        $response->assertOk();
        static::assertModelMissing($studioResource);
    }
}
