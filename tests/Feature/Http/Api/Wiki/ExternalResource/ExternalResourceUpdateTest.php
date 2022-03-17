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
 * Class ExternalResourceUpdateTest.
 */
class ExternalResourceUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The ExternalResource Update Endpoint shall be protected by sanctum.
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

        $response->assertForbidden();
    }

    /**
     * The ExternalResource Update Endpoint shall create an resource.
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

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['resource:update']
        );

        $response = $this->put(route('api.resource.update', ['resource' => $resource] + $parameters));

        $response->assertOk();
    }
}
