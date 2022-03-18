<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Song;

use App\Models\Auth\User;
use App\Models\Wiki\Song;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SongRestoreTest.
 */
class SongRestoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Song Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $song = Song::factory()->createOne();

        $song->delete();

        $response = $this->patch(route('api.song.restore', ['song' => $song]));

        $response->assertForbidden();
    }

    /**
     * The Song Restore Endpoint shall restore the song.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $song = Song::factory()->createOne();

        $song->delete();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['song:restore']
        );

        $response = $this->patch(route('api.song.restore', ['song' => $song]));

        $response->assertOk();
        static::assertNotSoftDeleted($song);
    }
}
