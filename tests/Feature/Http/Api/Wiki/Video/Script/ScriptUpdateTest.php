<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video\Script;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video\VideoScript;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ScriptUpdateTest.
 */
class ScriptUpdateTest extends TestCase
{
    /**
     * The Script Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $script = VideoScript::factory()->createOne();

        $parameters = VideoScript::factory()->raw();

        $response = $this->put(route('api.videoscript.update', ['videoscript' => $script] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Script Update Endpoint shall forbid users without the update video script permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $script = VideoScript::factory()->createOne();

        $parameters = VideoScript::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.videoscript.update', ['videoscript' => $script] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Script Update Endpoint shall forbid users from updating a script that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $script = VideoScript::factory()->trashed()->createOne();

        $parameters = VideoScript::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(VideoScript::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.videoscript.update', ['videoscript' => $script] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Script Update Endpoint shall update a script.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $script = VideoScript::factory()->createOne();

        $parameters = VideoScript::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(VideoScript::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.videoscript.update', ['videoscript' => $script] + $parameters));

        $response->assertOk();
    }
}
