<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Audio;

use App\Models\Auth\User;
use App\Models\Wiki\Audio;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AudioDestroyTest.
 */
class AudioDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Audio Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $audio = Audio::factory()->createOne();

        $response = $this->delete(route('api.audio.destroy', ['audio' => $audio]));

        $response->assertUnauthorized();
    }

    /**
     * The Audio Destroy Endpoint shall forbid users without the delete audio permission.
     *
     * @return void
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
     * The Audio Destroy Endpoint shall delete the audio.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $audio = Audio::factory()->createOne();

        $user = User::factory()->withPermission('delete audio')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.audio.destroy', ['audio' => $audio]));

        $response->assertOk();
        static::assertSoftDeleted($audio);
    }
}
