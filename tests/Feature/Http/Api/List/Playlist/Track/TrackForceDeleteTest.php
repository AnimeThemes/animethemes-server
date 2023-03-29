<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist\Track;

use App\Constants\Config\FlagConstants;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class TrackForceDeleteTest.
 */
class TrackForceDeleteTest extends TestCase
{
    use WithFaker;

    /**
     * The Track Force Delete Endpoint shall require authorization.
     *
     * @return void
     */
    public function testAuthorized(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $response = $this->delete(route('api.playlist.track.forceDelete', ['playlist' => $track->playlist, 'track' => $track]));

        $response->assertUnauthorized();
    }

    /**
     * The Track Force Delete Endpoint shall forbid users without the force delete playlist track permission.
     *
     * @return void
     */
    public function testForbiddenIfMissingPermission(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.forceDelete', ['playlist' => $track->playlist, 'track' => $track]));

        $response->assertForbidden();
    }

    /**
     * The Track Force Delete Endpoint shall scope bindings.
     *
     * @return void
     */
    public function testScoped(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE()->format(PlaylistTrack::class))->createOne();

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

        $response = $this->delete(route('api.playlist.track.forceDelete', ['playlist' => $playlist, 'track' => $track]));

        $response->assertNotFound();
    }

    /**
     * The Track Force Delete Endpoint shall forbid users from force deleting playlist tracks
     * if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, false);

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.forceDelete', ['playlist' => $playlist, 'track' => $track]));

        $response->assertForbidden();
    }

    /**
     * The Track Force Delete Endpoint shall force delete the sole playlist track.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.forceDelete', ['playlist' => $playlist, 'track' => $track]));

        $response->assertOk();

        static::assertModelMissing($track);

        $playlist->refresh();

        static::assertTrue($playlist->first()->doesntExist());
        static::assertTrue($playlist->last()->doesntExist());
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to force delete playlist tracks
     * even if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testDeletePermittedForBypass(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                ExtendedCrudPermission::FORCE_DELETE()->format(PlaylistTrack::class),
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

        $response = $this->delete(route('api.playlist.track.forceDelete', ['playlist' => $playlist, 'track' => $track]));

        $response->assertOk();
    }

    /**
     * The Track Force Delete Endpoint shall delete the first track.
     *
     * @return void
     */
    public function testForceDeleteFirst(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($this->faker->numberBetween(3, 9))
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.forceDelete', ['playlist' => $playlist, 'track' => $first]));

        $response->assertOk();

        static::assertModelMissing($first);

        $playlist->refresh();
        $second->refresh();

        static::assertTrue($playlist->first()->is($second));

        static::assertTrue($second->previous()->doesntExist());
    }

    /**
     * The Track Force Delete Endpoint shall delete the last track.
     *
     * @return void
     */
    public function testForceDeleteLast(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($this->faker->numberBetween(3, 9))
            ->createOne();

        $last = $playlist->last;
        $previous = $last->previous;

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.forceDelete', ['playlist' => $playlist, 'track' => $last]));

        $response->assertOk();

        static::assertModelMissing($last);

        $playlist->refresh();
        $previous->refresh();

        static::assertTrue($playlist->last()->is($previous));

        static::assertTrue($previous->next()->doesntExist());
    }

    /**
     * The Track Force Delete Endpoint shall delete the second track.
     *
     * @return void
     */
    public function testForceDeleteSecond(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlist.track.forceDelete', ['playlist' => $playlist, 'track' => $second]));

        $response->assertOk();

        static::assertModelMissing($second);

        $playlist->refresh();
        $first->refresh();
        $third->refresh();

        static::assertTrue($playlist->first()->is($first));
        static::assertTrue($playlist->last()->is($third));

        static::assertTrue($first->previous()->doesntExist());
        static::assertTrue($first->next()->is($third));

        static::assertTrue($third->previous()->is($first));
        static::assertTrue($third->next()->doesntExist());
    }
}
