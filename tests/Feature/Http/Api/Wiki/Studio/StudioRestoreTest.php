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
     * The Studio Restore Endpoint shall restore the studio.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $studio = Studio::factory()->createOne();

        $studio->delete();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['studio:restore']
        );

        $response = $this->patch(route('api.studio.restore', ['studio' => $studio]));

        $response->assertOk();
        static::assertNotSoftDeleted($studio);
    }
}
