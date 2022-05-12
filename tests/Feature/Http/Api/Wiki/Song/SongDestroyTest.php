<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Song;

use App\Models\Auth\User;
use App\Models\Wiki\Song;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SongDestroyTest.
 */
class SongDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Song Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $song = Song::factory()->createOne();

        $response = $this->delete(route('api.song.destroy', ['song' => $song]));

        $response->assertUnauthorized();
    }

    /**
     * The Song Destroy Endpoint shall delete the song.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $song = Song::factory()->createOne();

        $user = User::factory()->withPermission('delete song')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.song.destroy', ['song' => $song]));

        $response->assertOk();
        static::assertSoftDeleted($song);
    }
}
