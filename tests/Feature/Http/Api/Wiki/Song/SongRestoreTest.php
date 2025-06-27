<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Song;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Song;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SongRestoreTest.
 */
class SongRestoreTest extends TestCase
{
    /**
     * The Song Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $song = Song::factory()->trashed()->createOne();

        $response = $this->patch(route('api.song.restore', ['song' => $song]));

        $response->assertUnauthorized();
    }

    /**
     * The Song Restore Endpoint shall forbid users without the restore song permission.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $song = Song::factory()->trashed()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.song.restore', ['song' => $song]));

        $response->assertForbidden();
    }

    /**
     * The Song Restore Endpoint shall forbid users from restoring a song that isn't trashed.
     *
     * @return void
     */
    public function test_trashed(): void
    {
        $song = Song::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Song::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.song.restore', ['song' => $song]));

        $response->assertForbidden();
    }

    /**
     * The Song Restore Endpoint shall restore the song.
     *
     * @return void
     */
    public function test_restored(): void
    {
        $song = Song::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Song::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.song.restore', ['song' => $song]));

        $response->assertOk();
        static::assertNotSoftDeleted($song);
    }
}
