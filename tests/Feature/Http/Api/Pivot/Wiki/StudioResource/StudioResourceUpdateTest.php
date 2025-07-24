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

class StudioResourceUpdateTest extends TestCase
{
    /**
     * The Studio Resource Update Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $studioResource = StudioResource::factory()
            ->for(Studio::factory())
            ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
            ->createOne();

        $parameters = StudioResource::factory()->raw();

        $response = $this->put(route('api.studioresource.update', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Studio Resource Update Endpoint shall forbid users without the update studio & update resource permissions.
     */
    public function testForbidden(): void
    {
        $studioResource = StudioResource::factory()
            ->for(Studio::factory())
            ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
            ->createOne();

        $parameters = StudioResource::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.studioresource.update', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Studio Resource Update Endpoint shall update a studio resource.
     */
    public function testUpdate(): void
    {
        $studioResource = StudioResource::factory()
            ->for(Studio::factory())
            ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
            ->createOne();

        $parameters = StudioResource::factory()->raw();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::UPDATE->format(Studio::class),
                CrudPermission::UPDATE->format(ExternalResource::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.studioresource.update', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource] + $parameters));

        $response->assertOk();
    }
}
