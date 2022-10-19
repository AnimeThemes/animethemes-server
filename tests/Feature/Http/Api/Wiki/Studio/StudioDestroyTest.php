<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Studio;

use App\Models\Auth\User;
use App\Models\Wiki\Studio;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class StudioDestroyTest.
 */
class StudioDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Studio Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
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
    public function testForbidden(): void
    {
        $studio = Studio::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.studio.destroy', ['studio' => $studio]));

        $response->assertForbidden();
    }

    /**
     * The Studio Destroy Endpoint shall delete the studio.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $studio = Studio::factory()->createOne();

        $user = User::factory()->withPermission('delete studio')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.studio.destroy', ['studio' => $studio]));

        $response->assertOk();
        static::assertSoftDeleted($studio);
    }
}
