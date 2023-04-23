<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist\Track;

use App\Constants\Config\PlaylistConstants;
use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class TrackRestoreTest.
 */
class TrackRestoreTest extends TestCase
{
    use WithFaker;

    /**
     * The Track Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $response = $this->patch(route('api.playlist.track.restore', ['playlist' => $track->playlist, 'track' => $track]));

        $response->assertUnauthorized();
    }

    /**
     * The Track Restore Endpoint shall forbid users without the restore track permission.
     *
     * @return void
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

        $response = $this->patch(route('api.playlist.track.restore', ['playlist' => $track->playlist, 'track' => $track]));

        $response->assertForbidden();
    }

    /**
     * The Track Restore Endpoint shall forbid users from restoring the playlist track if they don't own the playlist.
     *
     * @return void
     */
    public function testForbiddenIfNotOwnPlaylist(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory()->for(User::factory()))
            ->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE()->format(PlaylistTrack::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.playlist.track.restore', ['playlist' => $track->playlist, 'track' => $track]));

        $response->assertForbidden();
    }

    /**
     * The Track Restore Endpoint shall scope bindings.
     *
     * @return void
     */
    public function testScoped(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE()->format(PlaylistTrack::class))->createOne();

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

        $response = $this->patch(route('api.playlist.track.restore', ['playlist' => $playlist, 'track' => $track]));

        $response->assertNotFound();
    }

    /**
     * The Track Restore Endpoint shall forbid users from restoring a playlist track that isn't trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE()->format(PlaylistTrack::class))->createOne();

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory()->for($user))
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.playlist.track.restore', ['playlist' => $track->playlist, 'track' => $track]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Restore Endpoint shall forbid users from restoring playlists
     * if the Allow Playlist Management feature is inactive.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::deactivate(AllowPlaylistManagement::class);

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE()->format(PlaylistTrack::class),
                ExtendedCrudPermission::RESTORE()->format(PlaylistTrack::class)
            )
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Sanctum::actingAs($user);

        $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

        $response = $this->patch(route('api.playlist.track.restore', ['playlist' => $playlist, 'track' => $track]));

        $response->assertForbidden();
    }

    /**
     * The Track Restore Endpoint shall restore the sole playlist track.
     *
     * @return void
     */
    public function testRestored(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE()->format(PlaylistTrack::class),
                ExtendedCrudPermission::RESTORE()->format(PlaylistTrack::class))
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Sanctum::actingAs($user);

        $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

        $response = $this->patch(route('api.playlist.track.restore', ['playlist' => $playlist, 'track' => $track]));

        $response->assertOk();

        static::assertNotSoftDeleted($track);

        $playlist->refresh();
        $track->refresh();

        static::assertTrue($playlist->first()->is($track));
        static::assertTrue($playlist->last()->is($track));

        static::assertTrue($track->previous()->doesntExist());
        static::assertTrue($track->next()->doesntExist());
    }

    /**
     * The Track Restore Endpoint shall restore the first playlist track.
     *
     * @return void
     */
    public function testRestoreFirst(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE()->format(PlaylistTrack::class),
                ExtendedCrudPermission::RESTORE()->format(PlaylistTrack::class)
            )
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($this->faker->numberBetween(3, 9))
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $last = $playlist->last;

        Sanctum::actingAs($user);

        $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $first]));

        $response = $this->patch(route('api.playlist.track.restore', ['playlist' => $playlist, 'track' => $first]));

        $response->assertOk();

        static::assertNotSoftDeleted($first);

        $playlist->refresh();
        $first->refresh();
        $second->refresh();
        $last->refresh();

        static::assertTrue($playlist->first()->is($second));
        static::assertTrue($playlist->last()->is($first));

        static::assertTrue($first->next()->doesntExist());
        static::assertTrue($first->previous()->is($last));

        static::assertTrue($second->previous()->doesntExist());

        static::assertTrue($last->next()->is($first));
    }

    /**
     * The Track Restore Endpoint shall restore the last playlist track.
     *
     * @return void
     */
    public function testRestoreLast(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE()->format(PlaylistTrack::class),
                ExtendedCrudPermission::RESTORE()->format(PlaylistTrack::class)
            )
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($this->faker->numberBetween(3, 9))
            ->createOne();

        $last = $playlist->last;
        $previous = $last->previous;

        Sanctum::actingAs($user);

        $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $last]));

        $response = $this->patch(route('api.playlist.track.restore', ['playlist' => $playlist, 'track' => $last]));

        $response->assertOk();

        static::assertNotSoftDeleted($last);

        $playlist->refresh();
        $last->refresh();
        $previous->refresh();

        static::assertTrue($playlist->last()->is($last));

        static::assertTrue($last->next()->doesntExist());
        static::assertTrue($last->previous()->is($previous));

        static::assertTrue($previous->next()->is($last));
    }

    /**
     * The Track Restore Endpoint shall restore the second playlist track.
     *
     * @return void
     */
    public function testRestoreSecond(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE()->format(PlaylistTrack::class),
                ExtendedCrudPermission::RESTORE()->format(PlaylistTrack::class)
            )
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        Sanctum::actingAs($user);

        $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $second]));

        $response = $this->patch(route('api.playlist.track.restore', ['playlist' => $playlist, 'track' => $second]));

        $response->assertOk();

        static::assertNotSoftDeleted($second);

        $playlist->refresh();
        $first->refresh();
        $second->refresh();
        $third->refresh();

        static::assertTrue($playlist->first()->is($first));
        static::assertTrue($playlist->last()->is($second));

        static::assertTrue($first->previous()->doesntExist());
        static::assertTrue($first->next()->is($third));

        static::assertTrue($second->previous()->is($third));
        static::assertTrue($second->next()->doesntExist());

        static::assertTrue($third->previous()->is($first));
        static::assertTrue($third->next()->is($second));
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to force delete playlist tracks
     * even if the Allow Playlist Management feature is inactive.
     *
     * @return void
     */
    public function testDeletePermittedForBypass(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE()->format(PlaylistTrack::class),
                ExtendedCrudPermission::RESTORE()->format(PlaylistTrack::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS
            )
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Sanctum::actingAs($user);

        $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

        $response = $this->patch(route('api.playlist.track.restore', ['playlist' => $playlist, 'track' => $track]));

        $response->assertOk();
    }

    /**
     * The Track Restore Endpoint shall forbid users from restoring playlist tracks that exceed the max track limit.
     *
     * @return void
     */
    public function testMaxTrackLimit(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        $trackLimit = $this->faker->randomDigitNotNull();

        Config::set(PlaylistConstants::MAX_TRACKS_QUALIFIED, $trackLimit);
        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE()->format(PlaylistTrack::class),
                ExtendedCrudPermission::RESTORE()->format(PlaylistTrack::class)
            )
            ->createOne();

        $playlist = Playlist::factory()
            ->tracks($trackLimit)
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Sanctum::actingAs($user);

        $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

        $response = $this->patch(route('api.playlist.track.restore', ['playlist' => $playlist, 'track' => $track]));

        $response->assertForbidden();
    }

    /**
     * The Track Restore Endpoint shall permit users with bypass feature flag permission
     * to restore playlist tracks that exceed the max track limit.
     *
     * @return void
     */
    public function testMaxTrackLimitPermittedForBypass(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        $trackLimit = $this->faker->randomDigitNotNull();

        Config::set(PlaylistConstants::MAX_TRACKS_QUALIFIED, $trackLimit);
        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE()->format(PlaylistTrack::class),
                ExtendedCrudPermission::RESTORE()->format(PlaylistTrack::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS
            )
            ->createOne();

        $playlist = Playlist::factory()
            ->tracks($trackLimit)
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Sanctum::actingAs($user);

        $this->delete(route('api.playlist.track.destroy', ['playlist' => $playlist, 'track' => $track]));

        $response = $this->patch(route('api.playlist.track.restore', ['playlist' => $playlist, 'track' => $track]));

        $response->assertOk();
    }
}
