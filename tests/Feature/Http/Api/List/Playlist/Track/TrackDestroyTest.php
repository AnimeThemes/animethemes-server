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
 * Class TrackDestroyTest.
 */
class TrackDestroyTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Track Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $response = $this->delete(route('api.playlist.playlisttrack.destroy', ['playlist' => $track->playlist, 'playlisttrack' => $track]));

        $response->assertUnauthorized();
    }

    /**
     * The Track Destroy Endpoint shall forbid users without the delete playlist track permission.
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

        $response = $this->delete(route('api.playlist.playlisttrack.destroy', ['playlist' => $track->playlist, 'playlisttrack' => $track]));

        $response->assertForbidden();
    }

    /**
     * The Track Destroy Endpoint shall forbid users from deleting the track if they don't own the playlist.
     *
     * @return void
     */
    public function testForbiddenIfNotOwnPlaylist(): void
    {
        $track = PlaylistTrack::factory()
            ->for(Playlist::factory()->for(User::factory()))
            ->createOne();

        $user = User::factory()->withPermission('delete playlist track')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.playlisttrack.destroy', ['playlist' => $track->playlist, 'playlisttrack' => $track]));

        $response->assertForbidden();
    }

    /**
     * The Track Destroy Endpoint shall scope bindings.
     *
     * @return void
     */
    public function testScoped(): void
    {
        $user = User::factory()->withPermission('delete playlist track')->createOne();

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

        $response = $this->delete(route('api.playlist.playlisttrack.destroy', ['playlist' => $playlist, 'playlisttrack' => $track]));

        $response->assertNotFound();
    }

    /**
     * The Track Destroy Endpoint shall forbid users from updating a track that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $user = User::factory()->withPermission('delete playlist track')->createOne();

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory()->for($user))
            ->createOne();

        $track->delete();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.playlisttrack.destroy', ['playlist' => $track->playlist, 'playlisttrack' => $track]));

        $response->assertNotFound();
    }

    /**
     * The Track Destroy Endpoint shall delete the track.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $user = User::factory()->withPermission('delete playlist track')->createOne();

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory()->for($user))
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.playlisttrack.destroy', ['playlist' => $track->playlist, 'playlisttrack' => $track]));

        $response->assertOk();
        static::assertSoftDeleted($track);
    }
}
