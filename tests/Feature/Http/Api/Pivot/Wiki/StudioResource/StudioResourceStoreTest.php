<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\StudioResource;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class StudioResourceStoreTest.
 */
class StudioResourceStoreTest extends TestCase
{
    /**
     * The Studio Resource Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $parameters = StudioResource::factory()->raw();

        $response = $this->post(route('api.studioresource.store', ['studio' => $studio, 'resource' => $resource] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Studio Resource Store Endpoint shall forbid users without the create studio & create resource permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $parameters = StudioResource::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.studioresource.store', ['studio' => $studio, 'resource' => $resource] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Studio Resource Store Endpoint shall create a studio resource.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $studio = Studio::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $parameters = StudioResource::factory()->raw();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE->format(Studio::class),
                CrudPermission::CREATE->format(ExternalResource::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.studioresource.store', ['studio' => $studio, 'resource' => $resource] + $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(StudioResource::class, 1);
    }
}
