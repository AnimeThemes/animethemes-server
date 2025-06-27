<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\ExternalResource;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ExternalResourceForceDeleteTest.
 */
class ExternalResourceForceDeleteTest extends TestCase
{
    /**
     * The External Resource Force Delete Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $resource = ExternalResource::factory()->createOne();

        $response = $this->delete(route('api.resource.forceDelete', ['resource' => $resource]));

        $response->assertUnauthorized();
    }

    /**
     * The External Resource Force Delete Endpoint shall forbid users without the force delete external resource permission.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $resource = ExternalResource::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.resource.forceDelete', ['resource' => $resource]));

        $response->assertForbidden();
    }

    /**
     * The External Resource Force Delete Endpoint shall force delete the resource.
     *
     * @return void
     */
    public function test_deleted(): void
    {
        $resource = ExternalResource::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(ExternalResource::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.resource.forceDelete', ['resource' => $resource]));

        $response->assertOk();
        static::assertModelMissing($resource);
    }
}
