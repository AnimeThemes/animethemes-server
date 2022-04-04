<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\ExternalResource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ExternalResourceStoreTest.
 */
class ExternalResourceStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The ExternalResource Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $resource = ExternalResource::factory()->makeOne();

        $response = $this->post(route('api.resource.store', $resource->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The ExternalResource Store Endpoint shall require link & site fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['resource:create']
        );

        $response = $this->post(route('api.resource.store'));

        $response->assertJsonValidationErrors([
            ExternalResource::ATTRIBUTE_LINK,
            ExternalResource::ATTRIBUTE_SITE,
        ]);
    }

    /**
     * The ExternalResource Store Endpoint shall create an resource.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = array_merge(
            ExternalResource::factory()->raw(),
            [ExternalResource::ATTRIBUTE_SITE => ResourceSite::getDescription(ResourceSite::OFFICIAL_SITE)],
        );

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['resource:create']
        );

        $response = $this->post(route('api.resource.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(ExternalResource::TABLE, 1);
    }
}
