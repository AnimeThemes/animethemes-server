<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Audio;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Audio;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AudioRestoreTest.
 */
class AudioRestoreTest extends TestCase
{
    /**
     * The Audio Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $audio = Audio::factory()->trashed()->createOne();

        $response = $this->patch(route('api.audio.restore', ['audio' => $audio]));

        $response->assertUnauthorized();
    }

    /**
     * The Audio Restore Endpoint shall forbid users without the restore audio permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $audio = Audio::factory()->trashed()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.audio.restore', ['audio' => $audio]));

        $response->assertForbidden();
    }

    /**
     * The Audio Restore Endpoint shall forbid users from restoring an audio that isn't trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $audio = Audio::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Audio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.audio.restore', ['audio' => $audio]));

        $response->assertForbidden();
    }

    /**
     * The Audio Restore Endpoint shall restore the audio.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $audio = Audio::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Audio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.audio.restore', ['audio' => $audio]));

        $response->assertOk();
        static::assertNotSoftDeleted($audio);
    }
}
