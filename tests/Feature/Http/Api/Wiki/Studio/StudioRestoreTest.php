<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Studio;

use App\Models\Auth\User;
use App\Models\Wiki\Studio;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class StudioRestoreTest.
 */
class StudioRestoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Studio Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $studio = Studio::factory()->createOne();

        $studio->delete();

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
        $studio = Studio::factory()->createOne();

        $studio->delete();

        $user = User::factory()->createOne();

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
        $studio = Studio::factory()->createOne();

        $studio->delete();

        $user = User::factory()->withPermission('restore studio')->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.studio.restore', ['studio' => $studio]));

        $response->assertOk();
        static::assertNotSoftDeleted($studio);
    }
}
