<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video\Script;

use App\Models\Auth\User;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ScriptUpdateTest.
 */
class ScriptUpdateTest extends TestCase
{
    use WithoutEvents;

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
     * The Script Update Endpoint shall update a script.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $script = VideoScript::factory()->createOne();

        $parameters = VideoScript::factory()->raw();

        $user = User::factory()->withPermission('update video script')->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.videoscript.update', ['videoscript' => $script] + $parameters));

        $response->assertOk();
    }
}
