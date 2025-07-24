<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video\Script;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video\VideoScript;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ScriptDestroyTest extends TestCase
{
    /**
     * The Script Destroy Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $script = VideoScript::factory()->createOne();

        $response = $this->delete(route('api.videoscript.destroy', ['videoscript' => $script]));

        $response->assertUnauthorized();
    }

    /**
     * The Script Destroy Endpoint shall forbid users without the delete video script permission.
     */
    public function testForbidden(): void
    {
        $script = VideoScript::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.videoscript.destroy', ['videoscript' => $script]));

        $response->assertForbidden();
    }

    /**
     * The Script Destroy Endpoint shall forbid users from updating a script that is trashed.
     */
    public function testTrashed(): void
    {
        $script = VideoScript::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(VideoScript::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.videoscript.destroy', ['videoscript' => $script]));

        $response->assertNotFound();
    }

    /**
     * The Script Destroy Endpoint shall delete the script.
     */
    public function testDeleted(): void
    {
        $script = VideoScript::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(VideoScript::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.videoscript.destroy', ['videoscript' => $script]));

        $response->assertOk();
        static::assertSoftDeleted($script);
    }
}
