<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video\Script;

use App\Models\Auth\User;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ScriptForceDeleteTest.
 */
class ScriptForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Script Force Delete Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $script = VideoScript::factory()->createOne();

        $response = $this->delete(route('api.videoscript.forceDelete', ['videoscript' => $script]));

        $response->assertUnauthorized();
    }

    /**
     * The Script Force Delete Endpoint shall forbid users without the force delete video script permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $script = VideoScript::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.videoscript.forceDelete', ['videoscript' => $script]));

        $response->assertForbidden();
    }

    /**
     * The Script Force Delete Endpoint shall force delete the script.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $script = VideoScript::factory()->createOne();

        $user = User::factory()->withPermission('force delete video script')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.videoscript.forceDelete', ['videoscript' => $script]));

        $response->assertOk();
        static::assertModelMissing($script);
    }
}
