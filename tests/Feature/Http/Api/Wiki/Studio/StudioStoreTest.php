<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Studio;

use App\Models\Auth\User;
use App\Models\Wiki\Studio;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class StudioStoreTest.
 */
class StudioStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Studio Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $studio = Studio::factory()->makeOne();

        $response = $this->post(route('api.studio.store', $studio->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Studio Store Endpoint shall require name & slug fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['studio:create']
        );

        $response = $this->post(route('api.studio.store'));

        $response->assertJsonValidationErrors([
            Studio::ATTRIBUTE_NAME,
            Studio::ATTRIBUTE_SLUG,
        ]);
    }

    /**
     * The Studio Store Endpoint shall create a studio.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = Studio::factory()->raw();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['studio:create']
        );

        $response = $this->post(route('api.studio.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Studio::TABLE, 1);
    }
}
