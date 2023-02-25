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

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $track->playlist, 'track' => $track]));

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

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $track->playlist, 'track' => $track]));

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

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $track->playlist, 'track' => $track]));

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
            ->has(PlaylistTrack::factory()->count($this->faker->randomDigitNotNull()), Playlist::RELATION_TRACKS)
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory()->for(User::factory()))
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

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

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $track->playlist, 'track' => $track]));

        $response->assertNotFound();
    }

    /**
     * The Track Destroy Endpoint shall delete the sole track.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $user = User::factory()->withPermission('delete playlist track')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

        $response->assertOk();

        static::assertSoftDeleted($track);

        $playlist->refresh();
        $track->refresh();

        static::assertTrue($playlist->first()->doesntExist());
        static::assertTrue($playlist->last()->doesntExist());

        static::assertTrue($track->previous()->doesntExist());
        static::assertTrue($track->next()->doesntExist());
    }

    /**
     * The Track Destroy Endpoint shall delete the first track.
     *
     * @return void
     */
    public function testDestroyFirst(): void
    {
        $user = User::factory()->withPermission('delete playlist track')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($this->faker->numberBetween(3, 9))
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $first]));

        $response->assertOk();

        static::assertSoftDeleted($first);

        $playlist->refresh();
        $first->refresh();
        $second->refresh();

        static::assertTrue($playlist->first()->is($second));

        static::assertTrue($first->previous()->doesntExist());
        static::assertTrue($first->next()->doesntExist());

        static::assertTrue($second->previous()->doesntExist());
    }

    /**
     * The Track Destroy Endpoint shall delete the last track.
     *
     * @return void
     */
    public function testDestroyLast(): void
    {
        $user = User::factory()->withPermission('delete playlist track')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($this->faker->numberBetween(3, 9))
            ->createOne();

        $last = $playlist->last;
        $previous = $last->previous;

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $last]));

        $response->assertOk();

        static::assertSoftDeleted($last);

        $playlist->refresh();
        $last->refresh();
        $previous->refresh();

        static::assertTrue($playlist->last()->is($previous));

        static::assertTrue($last->previous()->doesntExist());
        static::assertTrue($last->next()->doesntExist());

        static::assertTrue($previous->next()->doesntExist());
    }

    /**
     * The Track Destroy Endpoint shall delete the second track.
     *
     * @return void
     */
    public function testDestroySecond(): void
    {
        $user = User::factory()->withPermission('delete playlist track')->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $second]));

        $response->assertOk();

        static::assertSoftDeleted($second);

        $playlist->refresh();
        $first->refresh();
        $second->refresh();
        $third->refresh();

        static::assertTrue($playlist->first()->is($first));
        static::assertTrue($playlist->last()->is($third));

        static::assertTrue($first->previous()->doesntExist());
        static::assertTrue($first->next()->is($third));

        static::assertTrue($second->previous()->doesntExist());
        static::assertTrue($second->next()->doesntExist());

        static::assertTrue($third->previous()->is($first));
        static::assertTrue($third->next()->doesntExist());
    }
}
