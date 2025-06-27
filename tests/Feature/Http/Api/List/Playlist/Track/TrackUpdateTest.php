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
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class TrackUpdateTest.
 */
class TrackUpdateTest extends TestCase
{
    use WithFaker;

    /**
     * The Track Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

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
    public function test_forbidden_if_missing_permission(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

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
    public function test_forbidden_if_not_own_playlist(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

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

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Track Update Endpoint shall scope bindings.
     *
     * @return void
     */
    public function test_scoped(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->has(PlaylistTrack::factory()->count($this->faker->randomDigitNotNull()), Playlist::RELATION_TRACKS)
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
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
    public function test_scope_previous(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

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
                PlaylistTrack::RELATION_PREVIOUS => $previous->getRouteKey(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $response->assertJsonValidationErrors([
            PlaylistTrack::RELATION_PREVIOUS,
        ]);
    }

    /**
     * The Track Update Endpoint shall forbid the previous track to be itself.
     *
     * @return void
     */
    public function test_previous_is_not_self(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

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
                PlaylistTrack::RELATION_PREVIOUS => $track->getRouteKey(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $response->assertJsonValidationErrors([
            PlaylistTrack::RELATION_PREVIOUS,
        ]);
    }

    /**
     * The Track Update Endpoint shall restrict the next track to a track within the playlist.
     *
     * @return void
     */
    public function test_scope_next(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

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
                PlaylistTrack::RELATION_NEXT => $next->getRouteKey(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $response->assertJsonValidationErrors([
            PlaylistTrack::RELATION_NEXT,
        ]);
    }

    /**
     * The Track Update Endpoint shall forbid the next track to be itself.
     *
     * @return void
     */
    public function test_next_is_not_self(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

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
                PlaylistTrack::RELATION_NEXT => $track->getRouteKey(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $response->assertJsonValidationErrors([
            PlaylistTrack::RELATION_NEXT,
        ]);
    }

    /**
     * The Track Store Endpoint shall prohibit the next and previous fields from both being present.
     *
     * @return void
     */
    public function test_prohibits_next_and_previous(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

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
                PlaylistTrack::RELATION_NEXT => $next->getRouteKey(),
                PlaylistTrack::RELATION_PREVIOUS => $previous->getRouteKey(),
            ],
        );

        Sanctum::actingAs($user);

        $response = $this->put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $response->assertJsonValidationErrors([
            PlaylistTrack::RELATION_NEXT,
            PlaylistTrack::RELATION_PREVIOUS,
        ]);
    }

    /**
     * The Playlist Update Endpoint shall forbid users from updating playlists
     * if the Allow Playlist Management feature is inactive.
     *
     * @return void
     */
    public function test_forbidden_if_flag_disabled(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::deactivate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

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
    public function test_trashed(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();

        $track = PlaylistTrack::factory()
            ->trashed()
            ->for($playlist)
            ->createOne();

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
                PlaylistTrack::RELATION_PREVIOUS => $previous->getRouteKey(),
                PlaylistTrack::RELATION_NEXT => $next->getRouteKey(),
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
    public function test_update(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

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
    public function test_insert_first_after_second(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $parameters = [
            PlaylistTrack::RELATION_PREVIOUS => $second->getRouteKey(),
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
    public function test_insert_first_after_third(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $parameters = [
            PlaylistTrack::RELATION_PREVIOUS => $third->getRouteKey(),
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
    public function test_insert_first_before_third(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $parameters = [
            PlaylistTrack::RELATION_NEXT => $third->getRouteKey(),
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
    public function test_insert_second_after_third(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $parameters = [
            PlaylistTrack::RELATION_PREVIOUS => $third->getRouteKey(),
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
    public function test_insert_second_before_first(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $parameters = [
            PlaylistTrack::RELATION_NEXT => $first->getRouteKey(),
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
    public function test_insert_third_after_first(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $parameters = [
            PlaylistTrack::RELATION_PREVIOUS => $first->getRouteKey(),
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
    public function test_insert_third_before_second(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $parameters = [
            PlaylistTrack::RELATION_NEXT => $second->getRouteKey(),
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
    public function test_insert_third_before_first(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $parameters = [
            PlaylistTrack::RELATION_NEXT => $first->getRouteKey(),
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
     * even if the Allow Playlist Management feature is inactive.
     *
     * @return void
     */
    public function test_update_permitted_for_bypass(): void
    {
        Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

        Feature::activate(AllowPlaylistManagement::class, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                CrudPermission::UPDATE->format(PlaylistTrack::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS->value
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
