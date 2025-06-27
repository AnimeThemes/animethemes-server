<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Song;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Song;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SongForceDeleteTest.
 */
class SongForceDeleteTest extends TestCase
{
    /**
     * The Song Force Delete Endpoint shall be protected by sanctum.
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
     * The Song Force Delete Endpoint shall forbid users without the force delete song permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $song = Song::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.song.forceDelete', ['song' => $song]));

        $response->assertForbidden();
    }

    /**
     * The Song Force Delete Endpoint shall force delete the song.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $song = Song::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Song::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.song.forceDelete', ['song' => $song]));

        $response->assertOk();
        static::assertModelMissing($song);
    }
}
