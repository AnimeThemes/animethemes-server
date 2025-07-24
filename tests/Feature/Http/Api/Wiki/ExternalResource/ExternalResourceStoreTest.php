<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\ExternalResource;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExternalResourceStoreTest extends TestCase
{
    /**
     * The External Resource Store Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $resource = ExternalResource::factory()->makeOne();

        $response = $this->post(route('api.resource.store', $resource->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The External Resource Store Endpoint shall forbid users without the create external resource permission.
     */
    public function testForbidden(): void
    {
        $resource = ExternalResource::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.resource.store', $resource->toArray()));

        $response->assertForbidden();
    }

    /**
     * The External Resource Store Endpoint shall require link & site fields.
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(ExternalResource::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.resource.store'));

        $response->assertJsonValidationErrors([
            ExternalResource::ATTRIBUTE_LINK,
            ExternalResource::ATTRIBUTE_SITE,
        ]);
    }

    /**
     * The External Resource Store Endpoint shall create an resource.
     */
    public function testCreate(): void
    {
        $parameters = array_merge(
            ExternalResource::factory()->raw(),
            [ExternalResource::ATTRIBUTE_SITE => ResourceSite::OFFICIAL_SITE->localize()],
        );

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(ExternalResource::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.resource.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(ExternalResource::class, 1);
    }
}
