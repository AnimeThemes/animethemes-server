<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Song;

use App\Models\Auth\User;
use App\Models\Wiki\Song;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SongForceDeleteTest.
 */
class SongForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Song Force Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $song = Song::factory()->createOne();

        $response = $this->delete(route('api.song.forceDelete', ['song' => $song]));

        $response->assertUnauthorized();
    }

    /**
     * The Song Force Destroy Endpoint shall force delete the song.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $song = Song::factory()->createOne();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('admin')->createOne(),
            ['*']
        );

        $response = $this->delete(route('api.song.forceDelete', ['song' => $song]));

        $response->assertOk();
        static::assertModelMissing($song);
    }
}
