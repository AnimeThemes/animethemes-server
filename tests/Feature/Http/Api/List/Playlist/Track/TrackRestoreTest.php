<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist\Track;

use App\Enums\Models\List\PlaylistVisibility;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class TrackRestoreTest.
 */
class TrackRestoreTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Track Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $response = $this->patch(route('api.playlist.playlisttrack.restore', ['playlist' => $track->playlist, 'playlisttrack' => $track]));

        $response->assertUnauthorized();
    }

    /**
     * The Track Restore Endpoint shall forbid users without the restore track permission.
     *
     * @return void
     */
    public function testForbiddenIfMissingPermission(): void
    {
        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.playlist.playlisttrack.restore', ['playlist' => $track->playlist, 'playlisttrack' => $track]));

        $response->assertForbidden();
    }

    /**
     * The Track Restore Endpoint shall forbid users from restoring the playlist track if they don't own the playlist.
     *
     * @return void
     */
    public function testForbiddenIfNotOwnPlaylist(): void
    {
        $track = PlaylistTrack::factory()
            ->for(Playlist::factory()->for(User::factory()))
            ->createOne();

        $user = User::factory()->withPermission('restore playlist track')->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.playlist.playlisttrack.restore', ['playlist' => $track->playlist, 'playlisttrack' => $track]));

        $response->assertForbidden();
    }

    /**
     * The Track Restore Endpoint shall scope bindings.
     *
     * @return void
     */
    public function testScoped(): void
    {
        $user = User::factory()->withPermission('restore playlist track')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->has(PlaylistTrack::factory()->count($this->faker->randomDigitNotNull()))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory()->for(User::factory()))
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.playlist.playlisttrack.restore', ['playlist' => $playlist, 'playlisttrack' => $track]));

        $response->assertNotFound();
    }

    /**
     * The Track Restore Endpoint shall restore the playlist track.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $user = User::factory()->withPermission('restore playlist track')->createOne();

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory()->for($user))
            ->createOne();

        $track->delete();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.playlist.playlisttrack.restore', ['playlist' => $track->playlist, 'playlisttrack' => $track]));

        $response->assertOk();
        static::assertNotSoftDeleted($track);
    }
}
