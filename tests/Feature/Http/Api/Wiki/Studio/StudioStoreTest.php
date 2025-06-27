<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Studio;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Studio;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class StudioStoreTest.
 */
class StudioStoreTest extends TestCase
{
    /**
     * The Studio Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $studio = Studio::factory()->makeOne();

        $response = $this->post(route('api.studio.store', $studio->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Studio Store Endpoint shall forbid users without the create studio permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $studio = Studio::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

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
        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Studio::class))->createOne();

        Sanctum::actingAs($user);

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

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Studio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.studio.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Studio::class, 1);
    }
}
