<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\ExternalResource;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExternalResourceRestoreTest extends TestCase
{
    /**
     * The External Resource Restore Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $resource = ExternalResource::factory()->trashed()->createOne();

        $response = $this->patch(route('api.resource.restore', ['resource' => $resource]));

        $response->assertUnauthorized();
    }

    /**
     * The External Resource Restore Endpoint shall forbid users without the restore external resource permission.
     */
    public function testForbidden(): void
    {
        $resource = ExternalResource::factory()->trashed()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.resource.restore', ['resource' => $resource]));

        $response->assertForbidden();
    }

    /**
     * The External Resource Restore Endpoint shall forbid users from restoring a resource that isn't trashed.
     */
    public function testTrashed(): void
    {
        $resource = ExternalResource::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(ExternalResource::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.resource.restore', ['resource' => $resource]));

        $response->assertForbidden();
    }

    /**
     * The External Resource Restore Endpoint shall restore the resource.
     */
    public function testRestored(): void
    {
        $resource = ExternalResource::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(ExternalResource::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.resource.restore', ['resource' => $resource]));

        $response->assertOk();
        static::assertNotSoftDeleted($resource);
    }
}
