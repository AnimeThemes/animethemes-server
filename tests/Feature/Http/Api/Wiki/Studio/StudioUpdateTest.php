<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Studio;

use App\Models\Auth\User;
use App\Models\Wiki\Studio;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class StudioUpdateTest.
 */
class StudioUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Studio Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $studio = Studio::factory()->createOne();

        $parameters = Studio::factory()->raw();

        $response = $this->put(route('api.studio.update', ['studio' => $studio] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Studio Update Endpoint shall create a studio.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $studio = Studio::factory()->createOne();

        $parameters = Studio::factory()->raw();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['studio:update']
        );

        $response = $this->put(route('api.studio.update', ['studio' => $studio] + $parameters));

        $response->assertOk();
    }
}
