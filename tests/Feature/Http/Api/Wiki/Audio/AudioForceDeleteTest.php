<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Audio;

use App\Models\Auth\User;
use App\Models\Wiki\Audio;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AudioForceDeleteTest.
 */
class AudioForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Audio Force Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $audio = Audio::factory()->createOne();

        $response = $this->delete(route('api.audio.forceDelete', ['audio' => $audio]));

        $response->assertUnauthorized();
    }

    /**
     * The Audio Force Destroy Endpoint shall force delete the audio.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $audio = Audio::factory()->createOne();

        $user = User::factory()->withPermission('force delete audio')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.audio.forceDelete', ['audio' => $audio]));

        $response->assertOk();
        static::assertModelMissing($audio);
    }
}
