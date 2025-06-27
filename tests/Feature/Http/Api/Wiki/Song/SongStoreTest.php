<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Song;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Song;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SongStoreTest.
 */
class SongStoreTest extends TestCase
{
    /**
     * The Song Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $song = Song::factory()->makeOne();

        $response = $this->post(route('api.song.store', $song->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Song Store Endpoint shall forbid users without the create song permission.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $song = Song::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.song.store', $song->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Song Store Endpoint shall create a song.
     *
     * @return void
     */
    public function test_create(): void
    {
        $parameters = Song::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Song::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.song.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Song::class, 1);
    }
}
