<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist\Track;

use App\Enums\Models\List\PlaylistVisibility;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class TrackUpdateTest.
 */
class TrackUpdateTest extends TestCase
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
        $playlist = Playlist::factory()->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $parameters = array_merge(
            PlaylistTrack::factory()->raw(),
            [PlaylistTrack::ATTRIBUTE_VIDEO => Video::factory()->createOne()->getKey()],
        );

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Track Update Endpoint shall forbid users without the update playlist track permission.
     *
     * @return void
     */
    public function testForbiddenIfMissingPermission(): void
    {
        $playlist = Playlist::factory()->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $parameters = array_merge(
            PlaylistTrack::factory()->raw(),
            [PlaylistTrack::ATTRIBUTE_VIDEO => Video::factory()->createOne()->getKey()],
        );

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Track Update Endpoint shall forbid users from updating the track if they don't the playlist.
     *
     * @return void
     */
    public function testForbiddenIfNotOwnPlaylist(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $parameters = array_merge(
            PlaylistTrack::factory()->raw(),
            [PlaylistTrack::ATTRIBUTE_VIDEO => Video::factory()->createOne()->getKey()],
        );

        $user = User::factory()->withPermission('update playlist track')->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Track Update Endpoint shall scope bindings.
     *
     * @return void
     */
    public function testScoped(): void
    {
        $user = User::factory()->withPermission('update playlist track')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->has(PlaylistTrack::factory()->count($this->faker->randomDigitNotNull()), Playlist::RELATION_TRACKS)
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory()->for(User::factory()))
            ->createOne();

        $parameters = array_merge(
            PlaylistTrack::factory()->raw(),
            [PlaylistTrack::ATTRIBUTE_VIDEO => Video::factory()->createOne()->getKey()],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $response->assertNotFound();
    }

    /**
     * The Track Update Endpoint shall restrict the previous track to a track within the playlist.
     *
     * @return void
     */
    public function testScopePrevious(): void
    {
        $user = User::factory()->withPermission('update playlist track')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $previous = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $parameters = array_merge(
            PlaylistTrack::factory()->raw(),
            [
                PlaylistTrack::ATTRIBUTE_VIDEO => Video::factory()->createOne()->getKey(),
                PlaylistTrack::ATTRIBUTE_PREVIOUS => $previous->getKey(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $response->assertJsonValidationErrors([
            PlaylistTrack::ATTRIBUTE_PREVIOUS,
        ]);
    }

    /**
     * The Track Update Endpoint shall restrict the next track to a track within the playlist.
     *
     * @return void
     */
    public function testScopeNext(): void
    {
        $user = User::factory()->withPermission('update playlist track')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $next = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $parameters = array_merge(
            PlaylistTrack::factory()->raw(),
            [
                PlaylistTrack::ATTRIBUTE_VIDEO => Video::factory()->createOne()->getKey(),
                PlaylistTrack::ATTRIBUTE_NEXT => $next->getKey(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $response->assertJsonValidationErrors([
            PlaylistTrack::ATTRIBUTE_NEXT,
        ]);
    }

    /**
     * The Track Update Endpoint shall forbid users from updating a track that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $user = User::factory()->withPermission('update playlist track')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $track->delete();

        $previous = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $next = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $parameters = array_merge(
            PlaylistTrack::factory()->raw(),
            [
                PlaylistTrack::ATTRIBUTE_VIDEO => Video::factory()->createOne()->getKey(),
                PlaylistTrack::ATTRIBUTE_PREVIOUS => $previous->getKey(),
                PlaylistTrack::ATTRIBUTE_NEXT => $next->getKey(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Track Update Endpoint shall update a playlist track.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $user = User::factory()->withPermission('update playlist track')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $parameters = array_merge(
            PlaylistTrack::factory()->raw(),
            [
                PlaylistTrack::ATTRIBUTE_VIDEO => Video::factory()->createOne()->getKey(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $response->assertOk();
    }
}
