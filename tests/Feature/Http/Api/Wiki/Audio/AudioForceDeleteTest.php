<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Audio;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Audio;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AudioForceDeleteTest extends TestCase
{
    /**
     * The Audio Force Delete Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $audio = Audio::factory()->createOne();

        $response = $this->delete(route('api.audio.forceDelete', ['audio' => $audio]));

        $response->assertUnauthorized();
    }

    /**
     * The Audio Force Delete Endpoint shall forbid users without the force delete audio permission.
     */
    public function testForbidden(): void
    {
        $audio = Audio::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.audio.forceDelete', ['audio' => $audio]));

        $response->assertForbidden();
    }

    /**
     * The Audio Force Delete Endpoint shall force delete the audio.
     */
    public function testDeleted(): void
    {
        $audio = Audio::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Audio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.audio.forceDelete', ['audio' => $audio]));

        $response->assertOk();
        static::assertModelMissing($audio);
    }
}
