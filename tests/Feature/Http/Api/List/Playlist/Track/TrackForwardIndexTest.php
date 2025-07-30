<?php

declare(strict_types=1);

use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Auth\CrudPermission;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\List\PlaylistVisibility;
use App\Events\List\Playlist\PlaylistCreated;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\List\Playlist\ForwardBackwardSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\List\Playlist\Collection\TrackCollection;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Models\List\Playlist\ForwardPlaylistTrack;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('private playlist cannot be publicly viewed', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
        ]);

    $track = PlaylistTrack::query()->inRandomOrder()->first();

    $response = $this->get(route('api.playlist.track.forward', ['playlist' => $playlist, 'track' => $track]));

    $response->assertForbidden();
});

test('private playlist track cannot be publicly viewed if not owned', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
        ]);

    $track = PlaylistTrack::query()->inRandomOrder()->first();

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(PlaylistTrack::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->get(route('api.playlist.track.forward', ['playlist' => $playlist, 'track' => $track]));

    $response->assertForbidden();
});

test('private playlist track can be viewed by owner', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(PlaylistTrack::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
        ]);

    $track = PlaylistTrack::query()->inRandomOrder()->first();

    Sanctum::actingAs($user);

    $response = $this->get(route('api.playlist.track.forward', ['playlist' => $playlist, 'track' => $track]));

    $response->assertOk();
});

test('unlisted playlist track can be viewed', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::UNLISTED->value,
        ]);

    $track = PlaylistTrack::query()->inRandomOrder()->first();

    $response = $this->get(route('api.playlist.track.forward', ['playlist' => $playlist, 'track' => $track]));

    $response->assertOk();
});

test('public playlist track can be viewed', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $track = PlaylistTrack::query()->inRandomOrder()->first();

    $response = $this->get(route('api.playlist.track.forward', ['playlist' => $playlist, 'track' => $track]));

    $response->assertOk();
});

test('default', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $trackCount = fake()->numberBetween(2, 9);

    $playlist = Playlist::factory()
        ->tracks($trackCount)
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    /** @var ForwardPlaylistTrack $track */
    $track = ForwardPlaylistTrack::query()->inRandomOrder()->first();

    $response = $this->get(route('api.playlist.track.forward', ['playlist' => $playlist, 'track' => $track]));

    $tracks = $track->descendants()->get();

    $response->assertJsonCount($tracks->count(), TrackCollection::$wrap);

    $response->assertJson(
        json_decode(
            json_encode(
                new TrackCollection($tracks, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $track = PlaylistTrack::query()->inRandomOrder()->first();

    $response = $this->get(route('api.playlist.track.forward', ['playlist' => $playlist, 'track' => $track]));

    $response->assertJsonStructure([
        TrackCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $schema = new ForwardBackwardSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    /** @var ForwardPlaylistTrack $track */
    $track = ForwardPlaylistTrack::query()->inRandomOrder()->first();

    $response = $this->get(route('api.playlist.track.forward', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $tracks = $track->descendants()
        ->get()
        ->load($includedPaths->all());

    $response->assertJson(
        json_decode(
            json_encode(
                new TrackCollection($tracks, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $schema = new ForwardBackwardSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            TrackResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    /** @var ForwardPlaylistTrack $track */
    $track = ForwardPlaylistTrack::query()->inRandomOrder()->first();

    $response = $this->get(route('api.playlist.track.forward', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new TrackCollection($track->descendants()->get(), new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $schema = new ForwardBackwardSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $track = PlaylistTrack::query()->inRandomOrder()->first();

    $response = $this->get(route('api.playlist.track.forward', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $response->assertJsonValidationErrors([
        SortParser::param(),
    ]);
});

test('filters', function () {
    Event::fakeExcept([PlaylistCreated::class, TrackCreated::class]);

    $parameters = [
        FilterParser::param() => [
            BaseModel::ATTRIBUTE_CREATED_AT => fake()->date(),
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $track = PlaylistTrack::query()->inRandomOrder()->first();

    $response = $this->get(route('api.playlist.track.forward', ['playlist' => $playlist, 'track' => $track] + $parameters));

    $response->assertJsonValidationErrors([
        FilterParser::param(),
    ]);
});
