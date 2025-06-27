<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video\Script;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video\VideoScript;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ScriptStoreTest.
 */
class ScriptStoreTest extends TestCase
{
    /**
     * The Script Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
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
    public function test_forbidden(): void
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
    public function test_required_fields(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(VideoScript::class))->createOne();

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
    public function test_create(): void
    {
        $parameters = VideoScript::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(VideoScript::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.videoscript.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(VideoScript::class, 1);
    }
}
