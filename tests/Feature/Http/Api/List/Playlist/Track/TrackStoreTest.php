<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist\Track;

use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class TrackStoreTest.
 */
class TrackStoreTest extends TestCase
{
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
            ->makeOne();

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Track Store Endpoint shall forbid users without the create playlist track permission.
     *
     * @return void
     */
    public function testForbiddenIfMissingPermission(): void
    {
        $playlist = Playlist::factory()->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Track Store Endpoint shall forbid users from creating a track if they don't own the playlist.
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
            ->makeOne();

        $user = User::factory()->withPermission('create playlist track')->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Track Store Endpoint shall require the video field.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermission('create playlist track')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist]));

        $response->assertJsonValidationErrors([
            PlaylistTrack::ATTRIBUTE_VIDEO,
        ]);
    }

    /**
     * The Track Store Endpoint shall restrict the next track to a track within the playlist.
     *
     * @return void
     */
    public function testScopeNext(): void
    {
        $user = User::factory()->withPermission('create playlist track')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $next = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->makeOne([
                PlaylistTrack::ATTRIBUTE_VIDEO => Video::factory()->createOne()->getKey(),
                PlaylistTrack::ATTRIBUTE_NEXT => $next->getKey(),
            ]);

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertJsonValidationErrors([
            PlaylistTrack::ATTRIBUTE_NEXT,
        ]);
    }

    /**
     * The Track Store Endpoint shall restrict the next track to a track within the playlist.
     *
     * @return void
     */
    public function testScopePrevious(): void
    {
        $user = User::factory()->withPermission('create playlist track')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $previous = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->makeOne([
                PlaylistTrack::ATTRIBUTE_VIDEO => Video::factory()->createOne()->getKey(),
                PlaylistTrack::ATTRIBUTE_PREVIOUS => $previous->getKey(),
            ]);

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertJsonValidationErrors([
            PlaylistTrack::ATTRIBUTE_PREVIOUS,
        ]);
    }

    /**
     * The Track Store Endpoint shall create a playlist track.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $user = User::factory()->withPermission('create playlist track')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $previous = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $next = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->makeOne([
                PlaylistTrack::ATTRIBUTE_PREVIOUS => $previous->getKey(),
                PlaylistTrack::ATTRIBUTE_NEXT => $next->getKey(),
            ]);

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertCreated();
        static::assertDatabaseCount(PlaylistTrack::TABLE, 3);
        static::assertDatabaseHas(PlaylistTrack::TABLE, [PlaylistTrack::ATTRIBUTE_PLAYLIST => $playlist->getKey()]);
    }
}
