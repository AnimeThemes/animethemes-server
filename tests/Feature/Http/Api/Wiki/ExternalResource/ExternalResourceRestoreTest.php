<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\ExternalResource;

use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ExternalResourceRestoreTest.
 */
class ExternalResourceRestoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The ExternalResource Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $resource = ExternalResource::factory()->createOne();

        $resource->delete();

        $response = $this->patch(route('api.resource.restore', ['resource' => $resource]));

        $response->assertUnauthorized();
    }

    /**
     * The ExternalResource Restore Endpoint shall restore the resource.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $resource = ExternalResource::factory()->createOne();

        $resource->delete();

        $user = User::factory()->withPermission('restore external resource')->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.resource.restore', ['resource' => $resource]));

        $response->assertOk();
        static::assertNotSoftDeleted($resource);
    }
}
