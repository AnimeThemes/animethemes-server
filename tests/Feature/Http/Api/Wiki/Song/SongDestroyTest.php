<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Song;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Song;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SongDestroyTest extends TestCase
{
    /**
     * The Song Destroy Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $song = Song::factory()->createOne();

        $response = $this->delete(route('api.song.destroy', ['song' => $song]));

        $response->assertUnauthorized();
    }

    /**
     * The Song Destroy Endpoint shall forbid users without the delete song permission.
     */
    public function testForbidden(): void
    {
        $song = Song::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.song.destroy', ['song' => $song]));

        $response->assertForbidden();
    }

    /**
     * The Song Destroy Endpoint shall forbid users from updating a song that is trashed.
     */
    public function testTrashed(): void
    {
        $song = Song::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Song::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.song.destroy', ['song' => $song]));

        $response->assertNotFound();
    }

    /**
     * The Song Destroy Endpoint shall delete the song.
     */
    public function testDeleted(): void
    {
        $song = Song::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Song::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.song.destroy', ['song' => $song]));

        $response->assertOk();
        static::assertSoftDeleted($song);
    }
}
