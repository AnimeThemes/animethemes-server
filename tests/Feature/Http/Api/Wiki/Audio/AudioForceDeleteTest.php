<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Audio;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Audio;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AudioForceDeleteTest.
 */
class AudioForceDeleteTest extends TestCase
{
    /**
     * The Audio Force Delete Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $audio = Audio::factory()->createOne();

        $response = $this->delete(route('api.audio.forceDelete', ['audio' => $audio]));

        $response->assertUnauthorized();
    }

    /**
     * The Audio Force Delete Endpoint shall forbid users without the force delete audio permission.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $audio = Audio::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.audio.forceDelete', ['audio' => $audio]));

        $response->assertForbidden();
    }

    /**
     * The Audio Force Delete Endpoint shall force delete the audio.
     *
     * @return void
     */
    public function test_deleted(): void
    {
        $audio = Audio::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Audio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.audio.forceDelete', ['audio' => $audio]));

        $response->assertOk();
        static::assertModelMissing($audio);
    }
}
