<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist\Track;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TrackDestroyTest extends TestCase
{
    use WithFaker;

    /**
     * The Track Destroy Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $track->playlist, 'track' => $track]));

        $response->assertUnauthorized();
    }

    /**
     * The Track Destroy Endpoint shall forbid users without the delete playlist track permission.
     */
    public function testForbiddenIfMissingPermission(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

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
     */
    public function testForbiddenIfNotOwnPlaylist(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory()->for(User::factory()))
            ->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $track->playlist, 'track' => $track]));

        $response->assertForbidden();
    }

    /**
     * The Track Destroy Endpoint shall forbid users from destroying playlists tracks
     * if the Allow Playlist Management feature is inactive.
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::deactivate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

        $response->assertForbidden();
    }

    /**
     * The Track Destroy Endpoint shall scope bindings.
     */
    public function testScoped(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->has(PlaylistTrack::factory()->count($this->faker->randomDigitNotNull()), Playlist::RELATION_TRACKS)
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
            ]);

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory()->for(User::factory()))
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

        $response->assertNotFound();
    }

    /**
     * The Track Destroy Endpoint shall delete the sole track.
     */
    public function testDeleted(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

        $response->assertOk();

        $playlist->refresh();

        static::assertModelMissing($track);
        static::assertTrue($playlist->first()->doesntExist());
        static::assertTrue($playlist->last()->doesntExist());
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to destroy playlist tracks
     * even if the Allow Playlist Management feature is inactive.
     */
    public function testDestroyPermittedForBypass(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(PlaylistTrack::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS->value
            )
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

        $response->assertOk();
    }

    /**
     * The Track Destroy Endpoint shall delete the first track.
     */
    public function testDestroyFirst(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($this->faker->numberBetween(3, 9))
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $first]));

        $response->assertOk();

        $playlist->refresh();
        $second->refresh();

        static::assertModelMissing($first);
        static::assertTrue($playlist->first()->is($second));
        static::assertTrue($second->previous()->doesntExist());
    }

    /**
     * The Track Destroy Endpoint shall delete the last track.
     */
    public function testDestroyLast(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($this->faker->numberBetween(3, 9))
            ->createOne();

        $last = $playlist->last;
        $previous = $last->previous;

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $last]));

        $response->assertOk();

        $playlist->refresh();
        $previous->refresh();

        static::assertModelMissing($last);
        static::assertTrue($playlist->last()->is($previous));
        static::assertTrue($previous->next()->doesntExist());
    }

    /**
     * The Track Destroy Endpoint shall delete the second track.
     */
    public function testDestroySecond(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(PlaylistTrack::class))->createOne();

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

        $playlist->refresh();
        $first->refresh();
        $third->refresh();

        static::assertModelMissing($second);

        static::assertTrue($playlist->first()->is($first));
        static::assertTrue($playlist->last()->is($third));

        static::assertTrue($first->previous()->doesntExist());
        static::assertTrue($first->next()->is($third));

        static::assertTrue($third->previous()->is($first));
        static::assertTrue($third->next()->doesntExist());
    }
}
