<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Audio;

use App\Enums\Auth\CrudPermission;
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
     * The Audio Update Endpoint shall forbid users from updating an audio that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $audio = Audio::factory()->createOne();

        $audio->delete();

        $parameters = Audio::factory()->raw();

        $user = User::factory()->withPermission(CrudPermission::UPDATE()->format(Audio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.audio.update', ['audio' => $audio] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Audio Update Endpoint shall update an audio.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $audio = Audio::factory()->createOne();

        $parameters = Audio::factory()->raw();

        $user = User::factory()->withPermission(CrudPermission::UPDATE()->format(Audio::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.audio.update', ['audio' => $audio] + $parameters));

        $response->assertOk();
    }
}
