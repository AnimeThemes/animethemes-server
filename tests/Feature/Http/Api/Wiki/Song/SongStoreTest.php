<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Song;

use App\Models\Auth\User;
use App\Models\Wiki\Song;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SongStoreTest.
 */
class SongStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Song Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $song = Song::factory()->makeOne();

        $response = $this->post(route('api.song.store', $song->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Song Store Endpoint shall create a song.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = Song::factory()->raw();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['song:create']
        );

        $response = $this->post(route('api.song.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Song::TABLE, 1);
    }
}
