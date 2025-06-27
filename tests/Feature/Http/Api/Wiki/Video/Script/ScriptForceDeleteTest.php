<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video\Script;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video\VideoScript;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ScriptForceDeleteTest.
 */
class ScriptForceDeleteTest extends TestCase
{
    /**
     * The Script Force Delete Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
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
    public function test_forbidden(): void
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
    public function test_deleted(): void
    {
        $script = VideoScript::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(VideoScript::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.videoscript.forceDelete', ['videoscript' => $script]));

        $response->assertOk();
        static::assertModelMissing($script);
    }
}
