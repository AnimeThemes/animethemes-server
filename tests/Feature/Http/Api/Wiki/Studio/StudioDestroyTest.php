<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Studio;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Studio;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class StudioDestroyTest.
 */
class StudioDestroyTest extends TestCase
{
    /**
     * The Studio Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $studio = Studio::factory()->createOne();

        $response = $this->delete(route('api.studio.destroy', ['studio' => $studio]));

        $response->assertUnauthorized();
    }

    /**
     * The Studio Destroy Endpoint shall forbid users without the delete studio permission.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $studio = Studio::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.studio.destroy', ['studio' => $studio]));

        $response->assertForbidden();
    }

    /**
     * The Studio Destroy Endpoint shall forbid users from updating a studio that is trashed.
     *
     * @return void
     */
    public function test_trashed(): void
    {
        $studio = Studio::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Studio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.studio.destroy', ['studio' => $studio]));

        $response->assertNotFound();
    }

    /**
     * The Studio Destroy Endpoint shall delete the studio.
     *
     * @return void
     */
    public function test_deleted(): void
    {
        $studio = Studio::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Studio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.studio.destroy', ['studio' => $studio]));

        $response->assertOk();
        static::assertSoftDeleted($studio);
    }
}
