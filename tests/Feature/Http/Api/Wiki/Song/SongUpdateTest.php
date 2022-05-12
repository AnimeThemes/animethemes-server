<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Song;

use App\Models\Auth\User;
use App\Models\Wiki\Song;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SongUpdateTest.
 */
class SongUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Song Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $song = Song::factory()->createOne();

        $parameters = Song::factory()->raw();

        $response = $this->put(route('api.song.update', ['song' => $song] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Song Update Endpoint shall update a song.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $song = Song::factory()->createOne();

        $parameters = Song::factory()->raw();

        $user = User::factory()->createOne();

        $user->givePermissionTo('update song');

        Sanctum::actingAs($user);

        $response = $this->put(route('api.song.update', ['song' => $song] + $parameters));

        $response->assertOk();
    }
}
