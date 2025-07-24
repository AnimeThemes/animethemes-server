<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Song;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Song;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SongStoreTest extends TestCase
{
    /**
     * The Song Store Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $song = Song::factory()->makeOne();

        $response = $this->post(route('api.song.store', $song->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Song Store Endpoint shall forbid users without the create song permission.
     */
    public function testForbidden(): void
    {
        $song = Song::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.song.store', $song->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Song Store Endpoint shall create a song.
     */
    public function testCreate(): void
    {
        $parameters = Song::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Song::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.song.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Song::class, 1);
    }
}
