<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Audio;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Audio;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AudioDestroyTest extends TestCase
{
    /**
     * The Audio Destroy Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $audio = Audio::factory()->createOne();

        $response = $this->delete(route('api.audio.destroy', ['audio' => $audio]));

        $response->assertUnauthorized();
    }

    /**
     * The Audio Destroy Endpoint shall forbid users without the delete audio permission.
     */
    public function testForbidden(): void
    {
        $audio = Audio::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.audio.destroy', ['audio' => $audio]));

        $response->assertForbidden();
    }

    /**
     * The Audio Destroy Endpoint shall forbid users from updating an audio that is trashed.
     */
    public function testTrashed(): void
    {
        $audio = Audio::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Audio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.audio.destroy', ['audio' => $audio]));

        $response->assertNotFound();
    }

    /**
     * The Audio Destroy Endpoint shall delete the audio.
     */
    public function testDeleted(): void
    {
        $audio = Audio::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Audio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.audio.destroy', ['audio' => $audio]));

        $response->assertOk();
        static::assertSoftDeleted($audio);
    }
}
