<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Studio;

use App\Models\Auth\User;
use App\Models\Wiki\Studio;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class StudioForceDeleteTest.
 */
class StudioForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Studio Force Delete Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $studio = Studio::factory()->createOne();

        $response = $this->delete(route('api.studio.forceDelete', ['studio' => $studio]));

        $response->assertUnauthorized();
    }

    /**
     * The Studio Force Delete Endpoint shall forbid users without the force delete studio permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $studio = Studio::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.studio.forceDelete', ['studio' => $studio]));

        $response->assertForbidden();
    }

    /**
     * The Studio Force Delete Endpoint shall force delete the studio.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $studio = Studio::factory()->createOne();

        $user = User::factory()->withPermission('force delete studio')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.studio.forceDelete', ['studio' => $studio]));

        $response->assertOk();
        static::assertModelMissing($studio);
    }
}
