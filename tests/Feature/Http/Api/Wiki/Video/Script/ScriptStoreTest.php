<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video\Script;

use App\Models\Auth\User;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ScriptStoreTest.
 */
class ScriptStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Script Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $script = VideoScript::factory()->makeOne();

        $response = $this->post(route('api.videoscript.store', $script->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Script Store Endpoint shall forbid users without the create video script permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $script = VideoScript::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.videoscript.store', $script->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Script Store Endpoint shall require the path field.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermission('create video script')->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.videoscript.store'));

        $response->assertJsonValidationErrors([
            VideoScript::ATTRIBUTE_PATH,
        ]);
    }

    /**
     * The Script Store Endpoint shall create a script.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = VideoScript::factory()->raw();

        $user = User::factory()->withPermission('create video script')->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.videoscript.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(VideoScript::TABLE, 1);
    }
}
