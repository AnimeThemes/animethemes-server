<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\List\Playlist\TrackSchema;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('private playlist track cannot be publicly viewed', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
        ]);

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $response = get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track]));

    $response->assertForbidden();
});

test('private playlist track cannot be publicly viewed if not owned', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
        ]);

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(PlaylistTrack::class))->createOne();

    Sanctum::actingAs($user);

    $response = get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track]));

    $response->assertForbidden();
});

test('private playlist track can be viewed by owner', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(PlaylistTrack::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
        ]);

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    Sanctum::actingAs($user);

    $response = get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track]));

    $response->assertOk();
});

test('unlisted playlist track can be viewed', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::UNLISTED->value,
        ]);

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $response = get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track]));

    $response->assertOk();
});

test('public playlist track can be viewed', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $response = get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track]));

    $response->assertOk();
});

test('scoped', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(PlaylistTrack::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->has(PlaylistTrack::factory()->count(fake()->randomDigitNotNull()), Playlist::RELATION_TRACKS)
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $track = PlaylistTrack::factory()
        ->for(Playlist::factory()->for(User::factory()))
        ->createOne();

    $response = get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track]));

    $response->assertNotFound();
});

test('default', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $playlist = Playlist::factory()
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $response = get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track]));

    $track->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new TrackResource($track, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $schema = new TrackSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $playlist = Playlist::factory()
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->for(Video::factory())
        ->for(PlaylistTrack::factory()->for($playlist), PlaylistTrack::RELATION_PREVIOUS)
        ->for(PlaylistTrack::factory()->for($playlist), PlaylistTrack::RELATION_NEXT)
        ->createOne();

    $response = get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $track->unsetRelations()->load($includedPaths->all());

    $response->assertJson(
        json_decode(
            json_encode(
                new TrackResource($track, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $schema = new TrackSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            TrackResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $playlist = Playlist::factory()
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $response = get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $track->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new TrackResource($track, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
