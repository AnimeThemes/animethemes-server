<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Studio;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Studio;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class StudioRestoreTest.
 */
class StudioRestoreTest extends TestCase
{
    /**
     * The Studio Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $studio = Studio::factory()->trashed()->createOne();

        $response = $this->patch(route('api.studio.restore', ['studio' => $studio]));

        $response->assertUnauthorized();
    }

    /**
     * The Studio Restore Endpoint shall forbid users without the restore studio permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $studio = Studio::factory()->trashed()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.studio.restore', ['studio' => $studio]));

        $response->assertForbidden();
    }

    /**
     * The Studio Restore Endpoint shall forbid users from restoring a studio that isn't trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $studio = Studio::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Studio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.studio.restore', ['studio' => $studio]));

        $response->assertForbidden();
    }

    /**
     * The Studio Restore Endpoint shall restore the studio.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $studio = Studio::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Studio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.studio.restore', ['studio' => $studio]));

        $response->assertOk();
        static::assertNotSoftDeleted($studio);
    }
}
