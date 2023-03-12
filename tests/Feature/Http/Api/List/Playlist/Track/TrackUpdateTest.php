<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist\Track;

use App\Constants\Config\FlagConstants;
use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\List\PlaylistVisibility;
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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

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

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

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
     * The Track Update Endpoint shall forbid the previous track to be itself.
     *
     * @return void
     */
    public function testPreviousIsNotSelf(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

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
                PlaylistTrack::ATTRIBUTE_PREVIOUS => $track->getKey(),
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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

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
     * The Track Update Endpoint shall forbid the next track to be itself.
     *
     * @return void
     */
    public function testNextIsNotSelf(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

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
                PlaylistTrack::ATTRIBUTE_NEXT => $track->getKey(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $response->assertJsonValidationErrors([
            PlaylistTrack::ATTRIBUTE_NEXT,
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

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

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
            ->createOne();

        $parameters = array_merge(
            PlaylistTrack::factory()->raw(),
            [
                PlaylistTrack::ATTRIBUTE_NEXT => $next->getKey(),
                PlaylistTrack::ATTRIBUTE_PREVIOUS => $previous->getKey(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $response->assertJsonValidationErrors([
            PlaylistTrack::ATTRIBUTE_NEXT,
            PlaylistTrack::ATTRIBUTE_PREVIOUS,
        ]);
    }

    /**
     * The Playlist Update Endpoint shall forbid users from updating playlists
     * if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, false);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

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

        $response->assertForbidden();
    }

    /**
     * The Track Update Endpoint shall forbid users from updating a track that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

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

    /**
     * The Track Update Endpoint shall insert the first track after the second track.
     *
     * @return void
     */
    public function testInsertFirstAfterSecond(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $parameters = [
            PlaylistTrack::ATTRIBUTE_PREVIOUS => $second->getKey(),
        ];

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $first] + $parameters));

        $response->assertOk();

        $playlist->refresh();
        $first->refresh();
        $second->refresh();
        $third->refresh();

        static::assertTrue($playlist->first()->is($second));
        static::assertTrue($playlist->last()->is($third));

        static::assertTrue($first->previous()->is($second));
        static::assertTrue($first->next()->is($third));

        static::assertTrue($second->previous()->doesntExist());
        static::assertTrue($second->next()->is($first));

        static::assertTrue($third->previous()->is($first));
        static::assertTrue($third->next()->doesntExist());
    }

    /**
     * The Track Update Endpoint shall insert the first track after the third track.
     *
     * @return void
     */
    public function testInsertFirstAfterThird(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $parameters = [
            PlaylistTrack::ATTRIBUTE_PREVIOUS => $third->getKey(),
        ];

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $first] + $parameters));

        $response->assertOk();

        $playlist->refresh();
        $first->refresh();
        $second->refresh();
        $third->refresh();

        static::assertTrue($playlist->first()->is($second));
        static::assertTrue($playlist->last()->is($first));

        static::assertTrue($first->previous()->is($third));
        static::assertTrue($first->next()->doesntExist());

        static::assertTrue($second->previous()->doesntExist());
        static::assertTrue($second->next()->is($third));

        static::assertTrue($third->previous()->is($second));
        static::assertTrue($third->next()->is($first));
    }

    /**
     * The Track Update Endpoint shall insert the first track before the third track.
     *
     * @return void
     */
    public function testInsertFirstBeforeThird(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $parameters = [
            PlaylistTrack::ATTRIBUTE_NEXT => $third->getKey(),
        ];

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $first] + $parameters));

        $response->assertOk();

        $playlist->refresh();
        $first->refresh();
        $second->refresh();
        $third->refresh();

        static::assertTrue($playlist->first()->is($second));
        static::assertTrue($playlist->last()->is($third));

        static::assertTrue($first->previous()->is($second));
        static::assertTrue($first->next()->is($third));

        static::assertTrue($second->previous()->doesntExist());
        static::assertTrue($second->next()->is($first));

        static::assertTrue($third->previous()->is($first));
        static::assertTrue($third->next()->doesntExist());
    }

    /**
     * The Track Update Endpoint shall insert the second track after the third track.
     *
     * @return void
     */
    public function testInsertSecondAfterThird(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $parameters = [
            PlaylistTrack::ATTRIBUTE_PREVIOUS => $third->getKey(),
        ];

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $second] + $parameters));

        $response->assertOk();

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
     * The Track Update Endpoint shall insert the second track before the first track.
     *
     * @return void
     */
    public function testInsertSecondBeforeFirst(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $parameters = [
            PlaylistTrack::ATTRIBUTE_NEXT => $first->getKey(),
        ];

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $second] + $parameters));

        $response->assertOk();

        $playlist->refresh();
        $first->refresh();
        $second->refresh();
        $third->refresh();

        static::assertTrue($playlist->first()->is($second));
        static::assertTrue($playlist->last()->is($third));

        static::assertTrue($first->previous()->is($second));
        static::assertTrue($first->next()->is($third));

        static::assertTrue($second->previous()->doesntExist());
        static::assertTrue($second->next()->is($first));

        static::assertTrue($third->previous()->is($first));
        static::assertTrue($third->next()->doesntExist());
    }

    /**
     * The Track Update Endpoint shall insert the third track after the first track.
     *
     * @return void
     */
    public function testInsertThirdAfterFirst(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $parameters = [
            PlaylistTrack::ATTRIBUTE_PREVIOUS => $first->getKey(),
        ];

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $third] + $parameters));

        $response->assertOk();

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
     * The Track Update Endpoint shall insert the third track before the second track.
     *
     * @return void
     */
    public function testInsertThirdBeforeSecond(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $parameters = [
            PlaylistTrack::ATTRIBUTE_NEXT => $second->getKey(),
        ];

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $third] + $parameters));

        $response->assertOk();

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
     * The Track Update Endpoint shall insert the third track before the first track.
     *
     * @return void
     */
    public function testInsertThirdBeforeFirst(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $parameters = [
            PlaylistTrack::ATTRIBUTE_NEXT => $first->getKey(),
        ];

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $third] + $parameters));

        $response->assertOk();

        $playlist->refresh();
        $first->refresh();
        $second->refresh();
        $third->refresh();

        static::assertTrue($playlist->first()->is($third));
        static::assertTrue($playlist->last()->is($second));

        static::assertTrue($first->previous()->is($third));
        static::assertTrue($first->next()->is($second));

        static::assertTrue($second->previous()->is($first));
        static::assertTrue($second->next()->doesntExist());

        static::assertTrue($third->previous()->doesntExist());
        static::assertTrue($third->next()->is($first));
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to update playlist tracks
     * even if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testUpdatePermittedForBypass(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                CrudPermission::UPDATE()->format(PlaylistTrack::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS
            )
            ->createOne();

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
