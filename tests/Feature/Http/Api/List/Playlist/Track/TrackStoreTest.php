<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist\Track;

use App\Constants\Config\FlagConstants;
use App\Constants\Config\PlaylistConstants;
use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class TrackStoreTest.
 */
class TrackStoreTest extends TestCase
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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->makeOne();

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(PlaylistTrack::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Track Store Endpoint shall forbid users from creating playlist tracks
     * if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, false);

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->makeOne();

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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(PlaylistTrack::class))->createOne();

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
     * The Track Store Endpoint shall prohibit the next and previous fields from both being present.
     *
     * @return void
     */
    public function testProhibitsNextAndPrevious(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(PlaylistTrack::class))->createOne();

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

        $response->assertJsonValidationErrors([
            PlaylistTrack::ATTRIBUTE_NEXT,
            PlaylistTrack::ATTRIBUTE_PREVIOUS,
        ]);
    }

    /**
     * The Track Store Endpoint shall restrict the next track to a track within the playlist.
     *
     * @return void
     */
    public function testScopeNext(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(PlaylistTrack::class))->createOne();

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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(PlaylistTrack::class))->createOne();

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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->makeOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertCreated();

        $track = PlaylistTrack::query()->first();
        $playlist->refresh();

        static::assertDatabaseCount(PlaylistTrack::TABLE, 1);

        static::assertTrue($playlist->first()->is($track));
        static::assertTrue($playlist->last()->is($track));
    }

    /**
     * The Track Store Endpoint shall allow inserting after tracks including the last track.
     *
     * @return void
     */
    public function testCreateAfterLastTrack(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(PlaylistTrack::class))->createOne();

        $trackCount = $this->faker->numberBetween(2, 9);

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($trackCount)
            ->createOne();

        $last = $playlist->last;

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->makeOne([
                PlaylistTrack::ATTRIBUTE_PREVIOUS => $last->getKey(),
            ]);

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertCreated();

        /** @var PlaylistTrack $track */
        $track = PlaylistTrack::query()->latest()->first();
        $playlist->refresh();
        $last->refresh();

        static::assertDatabaseCount(PlaylistTrack::TABLE, $trackCount + 1);

        static::assertTrue($playlist->last()->is($track));

        static::assertTrue($last->next()->is($track));

        static::assertTrue($track->previous()->is($last));
        static::assertTrue($track->next()->doesntExist());
    }

    /**
     * The Track Store Endpoint shall allow inserting after tracks including the first track.
     *
     * @return void
     */
    public function testCreateAfterFirstTrack(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(PlaylistTrack::class))->createOne();

        $trackCount = $this->faker->numberBetween(2, 9);

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($trackCount)
            ->createOne();

        $first = $playlist->first;
        $next = $first->next;

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->makeOne([
                PlaylistTrack::ATTRIBUTE_PREVIOUS => $first->getKey(),
            ]);

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertCreated();

        /** @var PlaylistTrack $track */
        $track = PlaylistTrack::query()->latest()->first();
        $playlist->refresh();
        $first->refresh();

        static::assertDatabaseCount(PlaylistTrack::TABLE, $trackCount + 1);

        static::assertTrue($playlist->first()->is($first));

        static::assertTrue($first->next()->is($track));

        static::assertTrue($track->previous()->is($first));
        static::assertTrue($track->next()->is($next));
    }

    /**
     * The Track Store Endpoint shall allow inserting before tracks including the last track.
     *
     * @return void
     */
    public function testCreateBeforeLastTrack(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(PlaylistTrack::class))->createOne();

        $trackCount = $this->faker->numberBetween(2, 9);

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($trackCount)
            ->createOne();

        $last = $playlist->last;
        $previous = $last->previous;

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->makeOne([
                PlaylistTrack::ATTRIBUTE_NEXT => $last->getKey(),
            ]);

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertCreated();

        /** @var PlaylistTrack $track */
        $track = PlaylistTrack::query()->latest()->first();
        $playlist->refresh();
        $last->refresh();

        static::assertDatabaseCount(PlaylistTrack::TABLE, $trackCount + 1);

        static::assertTrue($playlist->last()->is($last));

        static::assertTrue($last->previous()->is($track));

        static::assertTrue($track->previous()->is($previous));
        static::assertTrue($track->next()->is($last));
    }

    /**
     * The Track Store Endpoint shall allow inserting before tracks including the first track.
     *
     * @return void
     */
    public function testCreateBeforeFirstTrack(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(PlaylistTrack::class))->createOne();

        $trackCount = $this->faker->numberBetween(2, 9);

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($trackCount)
            ->createOne();

        $first = $playlist->first;

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->makeOne([
                PlaylistTrack::ATTRIBUTE_NEXT => $first->getKey(),
            ]);

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertCreated();

        /** @var PlaylistTrack $track */
        $track = PlaylistTrack::query()->latest()->first();
        $playlist->refresh();
        $first->refresh();

        static::assertDatabaseCount(PlaylistTrack::TABLE, $trackCount + 1);

        static::assertTrue($playlist->first()->is($track));

        static::assertTrue($track->previous()->doesntExist());
        static::assertTrue($track->next()->is($first));
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to create playlist tracks
     * even if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testCreatePermittedForBypass(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(PlaylistTrack::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS
            )
            ->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->makeOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertCreated();
    }

    /**
     * The Track Store Endpoint shall forbid users from creating playlists that exceed the max track limit.
     *
     * @return void
     */
    public function testMaxTrackLimit(): void
    {
        $trackLimit = $this->faker->randomDigitNotNull();

        Config::set(PlaylistConstants::MAX_TRACKS_QUALIFIED, $trackLimit);
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->tracks($trackLimit)
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->makeOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Track Store Endpoint shall permit users with bypass feature flag permission
     * to create playlists that exceed the max track limit.
     *
     * @return void
     */
    public function testMaxTrackLimitPermittedForBypass(): void
    {
        $trackLimit = $this->faker->randomDigitNotNull();

        Config::set(PlaylistConstants::MAX_TRACKS_QUALIFIED, $trackLimit);
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(PlaylistTrack::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS
            )
            ->createOne();

        $playlist = Playlist::factory()
            ->tracks($trackLimit)
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->makeOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlist.track.store', ['playlist' => $playlist] + $track->toArray()));

        $response->assertCreated();
    }
}
