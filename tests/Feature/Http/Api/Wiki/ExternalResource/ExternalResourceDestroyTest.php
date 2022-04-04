<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\ExternalResource;

use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ExternalResourceDestroyTest.
 */
class ExternalResourceDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The ExternalResource Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $resource = ExternalResource::factory()->createOne();

        $response = $this->delete(route('api.resource.destroy', ['resource' => $resource]));

        $response->assertUnauthorized();
    }

    /**
     * The ExternalResource Destroy Endpoint shall delete the resource.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $resource = ExternalResource::factory()->createOne();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['resource:delete']
        );

        $response = $this->delete(route('api.resource.destroy', ['resource' => $resource]));

        $response->assertOk();
        static::assertSoftDeleted($resource);
    }
}
