<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Audio;

use App\Models\Auth\User;
use App\Models\Wiki\Audio;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AudioUpdateTest.
 */
class AudioUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Audio Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $audio = Audio::factory()->createOne();

        $parameters = Audio::factory()->raw();

        $response = $this->put(route('api.audio.update', ['audio' => $audio] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Audio Update Endpoint shall forbid users without the update audio permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $audio = Audio::factory()->createOne();

        $parameters = Audio::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.audio.update', ['audio' => $audio] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Audio Update Endpoint shall update a audio.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $audio = Audio::factory()->createOne();

        $parameters = Audio::factory()->raw();

        $user = User::factory()->withPermission('update audio')->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.audio.update', ['audio' => $audio] + $parameters));

        $response->assertOk();
    }
}
