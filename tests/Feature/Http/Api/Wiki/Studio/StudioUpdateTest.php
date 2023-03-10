<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Studio;

use App\Enums\Auth\CrudPermission;
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

        $response->assertUnauthorized();
    }

    /**
     * The Studio Update Endpoint shall forbid users without the update studio permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $studio = Studio::factory()->createOne();

        $parameters = Studio::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.studio.update', ['studio' => $studio] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Studio Update Endpoint shall forbid users from updating a studio that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $studio = Studio::factory()->createOne();

        $studio->delete();

        $parameters = Studio::factory()->raw();

        $user = User::factory()->withPermission(CrudPermission::UPDATE()->format(Studio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.studio.update', ['studio' => $studio] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Studio Update Endpoint shall update a studio.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $studio = Studio::factory()->createOne();

        $parameters = Studio::factory()->raw();

        $user = User::factory()->withPermission(CrudPermission::UPDATE()->format(Studio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.studio.update', ['studio' => $studio] + $parameters));

        $response->assertOk();
    }
}
