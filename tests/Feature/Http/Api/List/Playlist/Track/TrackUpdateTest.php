<?php

declare(strict_types=1);

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
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('protected', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden if missing permission', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $response->assertForbidden();
});

test('forbidden if not own playlist', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $response->assertForbidden();
});

test('scoped', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(PlaylistTrack::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->has(PlaylistTrack::factory()->count(fake()->randomDigitNotNull()), Playlist::RELATION_TRACKS)
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $response->assertNotFound();
});

test('scope previous', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $response->assertJsonValidationErrors([
        PlaylistTrack::RELATION_PREVIOUS,
    ]);
});

test('previous is not self', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $response->assertJsonValidationErrors([
        PlaylistTrack::RELATION_PREVIOUS,
    ]);
});

test('scope next', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $response->assertJsonValidationErrors([
        PlaylistTrack::RELATION_NEXT,
    ]);
});

test('next is not self', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $response->assertJsonValidationErrors([
        PlaylistTrack::RELATION_NEXT,
    ]);
});

test('prohibits next and previous', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $response->assertJsonValidationErrors([
        PlaylistTrack::RELATION_NEXT,
        PlaylistTrack::RELATION_PREVIOUS,
    ]);
});

test('forbidden if flag disabled', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $response->assertOk();
});

test('insert first after second', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $first] + $parameters));

    $response->assertOk();

    $playlist->refresh();
    $first->refresh();
    $second->refresh();
    $third->refresh();

    $this->assertTrue($playlist->first()->is($second));
    $this->assertTrue($playlist->last()->is($third));

    $this->assertTrue($first->previous()->is($second));
    $this->assertTrue($first->next()->is($third));

    $this->assertTrue($second->previous()->doesntExist());
    $this->assertTrue($second->next()->is($first));

    $this->assertTrue($third->previous()->is($first));
    $this->assertTrue($third->next()->doesntExist());
});

test('insert first after third', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $first] + $parameters));

    $response->assertOk();

    $playlist->refresh();
    $first->refresh();
    $second->refresh();
    $third->refresh();

    $this->assertTrue($playlist->first()->is($second));
    $this->assertTrue($playlist->last()->is($first));

    $this->assertTrue($first->previous()->is($third));
    $this->assertTrue($first->next()->doesntExist());

    $this->assertTrue($second->previous()->doesntExist());
    $this->assertTrue($second->next()->is($third));

    $this->assertTrue($third->previous()->is($second));
    $this->assertTrue($third->next()->is($first));
});

test('insert first before third', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $first] + $parameters));

    $response->assertOk();

    $playlist->refresh();
    $first->refresh();
    $second->refresh();
    $third->refresh();

    $this->assertTrue($playlist->first()->is($second));
    $this->assertTrue($playlist->last()->is($third));

    $this->assertTrue($first->previous()->is($second));
    $this->assertTrue($first->next()->is($third));

    $this->assertTrue($second->previous()->doesntExist());
    $this->assertTrue($second->next()->is($first));

    $this->assertTrue($third->previous()->is($first));
    $this->assertTrue($third->next()->doesntExist());
});

test('insert second after third', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $second] + $parameters));

    $response->assertOk();

    $playlist->refresh();
    $first->refresh();
    $second->refresh();
    $third->refresh();

    $this->assertTrue($playlist->first()->is($first));
    $this->assertTrue($playlist->last()->is($second));

    $this->assertTrue($first->previous()->doesntExist());
    $this->assertTrue($first->next()->is($third));

    $this->assertTrue($second->previous()->is($third));
    $this->assertTrue($second->next()->doesntExist());

    $this->assertTrue($third->previous()->is($first));
    $this->assertTrue($third->next()->is($second));
});

test('insert second before first', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $second] + $parameters));

    $response->assertOk();

    $playlist->refresh();
    $first->refresh();
    $second->refresh();
    $third->refresh();

    $this->assertTrue($playlist->first()->is($second));
    $this->assertTrue($playlist->last()->is($third));

    $this->assertTrue($first->previous()->is($second));
    $this->assertTrue($first->next()->is($third));

    $this->assertTrue($second->previous()->doesntExist());
    $this->assertTrue($second->next()->is($first));

    $this->assertTrue($third->previous()->is($first));
    $this->assertTrue($third->next()->doesntExist());
});

test('insert third after first', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $third] + $parameters));

    $response->assertOk();

    $playlist->refresh();
    $first->refresh();
    $second->refresh();
    $third->refresh();

    $this->assertTrue($playlist->first()->is($first));
    $this->assertTrue($playlist->last()->is($second));

    $this->assertTrue($first->previous()->doesntExist());
    $this->assertTrue($first->next()->is($third));

    $this->assertTrue($second->previous()->is($third));
    $this->assertTrue($second->next()->doesntExist());

    $this->assertTrue($third->previous()->is($first));
    $this->assertTrue($third->next()->is($second));
});

test('insert third before second', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $third] + $parameters));

    $response->assertOk();

    $playlist->refresh();
    $first->refresh();
    $second->refresh();
    $third->refresh();

    $this->assertTrue($playlist->first()->is($first));
    $this->assertTrue($playlist->last()->is($second));

    $this->assertTrue($first->previous()->doesntExist());
    $this->assertTrue($first->next()->is($third));

    $this->assertTrue($second->previous()->is($third));
    $this->assertTrue($second->next()->doesntExist());

    $this->assertTrue($third->previous()->is($first));
    $this->assertTrue($third->next()->is($second));
});

test('insert third before first', function () {
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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $third] + $parameters));

    $response->assertOk();

    $playlist->refresh();
    $first->refresh();
    $second->refresh();
    $third->refresh();

    $this->assertTrue($playlist->first()->is($third));
    $this->assertTrue($playlist->last()->is($second));

    $this->assertTrue($first->previous()->is($third));
    $this->assertTrue($first->next()->is($second));

    $this->assertTrue($second->previous()->is($first));
    $this->assertTrue($second->next()->doesntExist());

    $this->assertTrue($third->previous()->doesntExist());
    $this->assertTrue($third->next()->is($first));
});

test('update permitted for bypass', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    Feature::activate(AllowPlaylistManagement::class, fake()->boolean());

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

    $response = put(route('api.playlist.track.update', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $response->assertOk();
});
