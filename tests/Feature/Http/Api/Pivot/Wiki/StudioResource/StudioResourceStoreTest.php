<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\StudioResource;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class StudioResourceStoreTest.
 */
class StudioResourceStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Studio Resource Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $studioResource = StudioResource::factory()
            ->for(Studio::factory())
            ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
            ->makeOne();

        $response = $this->post(route('api.studioresource.store', $studioResource->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Studio Resource Store Endpoint shall forbid users without the create studio & create resource permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $studioResource = StudioResource::factory()
            ->for(Studio::factory())
            ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
            ->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.studioresource.store', $studioResource->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Studio Resource Store Endpoint shall require studio and resource fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions([CrudPermission::CREATE()->format(Studio::class), CrudPermission::CREATE()->format(ExternalResource::class)])->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.studioresource.store'));

        $response->assertJsonValidationErrors([
            StudioResource::ATTRIBUTE_STUDIO,
            StudioResource::ATTRIBUTE_RESOURCE,
        ]);
    }

    /**
     * The Studio Resource Store Endpoint shall create a studio resource.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = array_merge(
            StudioResource::factory()->raw(),
            [StudioResource::ATTRIBUTE_STUDIO => Studio::factory()->createOne()->getKey()],
            [StudioResource::ATTRIBUTE_RESOURCE => ExternalResource::factory()->createOne()->getKey()],
        );

        $user = User::factory()->withPermissions([CrudPermission::CREATE()->format(Studio::class), CrudPermission::CREATE()->format(ExternalResource::class)])->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.studioresource.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(StudioResource::TABLE, 1);
    }
}
