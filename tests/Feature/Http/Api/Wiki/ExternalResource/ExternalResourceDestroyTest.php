<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\ExternalResource;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ExternalResourceDestroyTest.
 */
class ExternalResourceDestroyTest extends TestCase
{
    /**
     * The External Resource Destroy Endpoint shall be protected by sanctum.
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
     * The External Resource Destroy Endpoint shall forbid users without the delete external resource permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $resource = ExternalResource::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.resource.destroy', ['resource' => $resource]));

        $response->assertForbidden();
    }

    /**
     * The External Resource Destroy Endpoint shall forbid users from updating a resource that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $resource = ExternalResource::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(ExternalResource::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.resource.destroy', ['resource' => $resource]));

        $response->assertNotFound();
    }

    /**
     * The External Resource Destroy Endpoint shall delete the resource.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $resource = ExternalResource::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(ExternalResource::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.resource.destroy', ['resource' => $resource]));

        $response->assertOk();
        static::assertSoftDeleted($resource);
    }
}
