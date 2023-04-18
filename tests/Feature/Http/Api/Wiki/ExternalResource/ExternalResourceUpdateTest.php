<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\ExternalResource;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ExternalResourceUpdateTest.
 */
class ExternalResourceUpdateTest extends TestCase
{
    /**
     * The External Resource Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $resource = ExternalResource::factory()->createOne();

        $parameters = array_merge(
            ExternalResource::factory()->raw(),
            [ExternalResource::ATTRIBUTE_SITE => ResourceSite::getDescription(ResourceSite::OFFICIAL_SITE)]
        );

        $response = $this->put(route('api.resource.update', ['resource' => $resource] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The External Resource Update Endpoint shall forbid users without the update external resource permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $resource = ExternalResource::factory()->createOne();

        $parameters = array_merge(
            ExternalResource::factory()->raw(),
            [ExternalResource::ATTRIBUTE_SITE => ResourceSite::getDescription(ResourceSite::OFFICIAL_SITE)]
        );

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.resource.update', ['resource' => $resource] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The External Resource Update Endpoint shall forbid users from updating a resource that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::OFFICIAL_SITE,
        ]);

        $resource->delete();

        $parameters = array_merge(
            ExternalResource::factory()->raw(),
            [ExternalResource::ATTRIBUTE_SITE => ResourceSite::getDescription(ResourceSite::OFFICIAL_SITE)]
        );

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(ExternalResource::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.resource.update', ['resource' => $resource] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The External Resource Update Endpoint shall update a resource.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::OFFICIAL_SITE,
        ]);

        $parameters = array_merge(
            ExternalResource::factory()->raw(),
            [ExternalResource::ATTRIBUTE_SITE => ResourceSite::getDescription(ResourceSite::OFFICIAL_SITE)]
        );

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(ExternalResource::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.resource.update', ['resource' => $resource] + $parameters));

        $response->assertOk();
    }
}
