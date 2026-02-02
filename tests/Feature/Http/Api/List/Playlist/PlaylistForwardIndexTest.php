<?php

declare(strict_types=1);

use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Auth\CrudPermission;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\List\PlaylistVisibility;
use App\Events\List\Playlist\PlaylistCreated;
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
use App\Http\Resources\List\Playlist\Resource\TrackJsonResource;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('private playlist cannot be publicly viewed', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
        ]);

    $response = get(route('api.playlist.forward', ['playlist' => $playlist]));

    $response->assertForbidden();
});

test('private playlist track cannot be publicly viewed if not owned', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
        ]);

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(PlaylistTrack::class))->createOne();

    Sanctum::actingAs($user);

    $response = get(route('api.playlist.forward', ['playlist' => $playlist]));

    $response->assertForbidden();
});

test('private playlist track can be viewed by owner', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(PlaylistTrack::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
        ]);

    Sanctum::actingAs($user);

    $response = get(route('api.playlist.forward', ['playlist' => $playlist]));

    $response->assertOk();
});

test('unlisted playlist track can be viewed', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::UNLISTED->value,
        ]);

    $response = get(route('api.playlist.forward', ['playlist' => $playlist]));

    $response->assertOk();
});

test('public playlist track can be viewed', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $response = get(route('api.playlist.forward', ['playlist' => $playlist]));

    $response->assertOk();
});

test('default', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $trackCount = fake()->numberBetween(2, 9);

    $playlist = Playlist::factory()
        ->tracks($trackCount)
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    Collection::times(
        fake()->randomDigitNotNull(),
        fn () => Playlist::factory()
            ->has(PlaylistTrack::factory()->count($trackCount), Playlist::RELATION_TRACKS)
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
            ])
    );

    $response = get(route('api.playlist.forward', ['playlist' => $playlist]));

    $response->assertJsonCount($trackCount, TrackCollection::$wrap);

    $response->assertJson(
        json_decode(
            json_encode(
                new TrackCollection($playlist->tracks, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $response = get(route('api.playlist.forward', ['playlist' => $playlist]));

    $response->assertJsonStructure([
        TrackCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    Event::fakeExcept(PlaylistCreated::class);

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

    $response = get(route('api.playlist.forward', ['playlist' => $playlist] + $parameters));

    $tracks = PlaylistTrack::with($includedPaths->all())->get();

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
    Event::fakeExcept(PlaylistCreated::class);

    $schema = new ForwardBackwardSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            TrackJsonResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $response = get(route('api.playlist.forward', ['playlist' => $playlist] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new TrackCollection($playlist->tracks, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function () {
    Event::fakeExcept(PlaylistCreated::class);

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

    $response = get(route('api.playlist.forward', ['playlist' => $playlist] + $parameters));

    $response->assertJsonValidationErrors([
        SortParser::param(),
    ]);
});

test('filters', function () {
    Event::fakeExcept(PlaylistCreated::class);

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

    $response = get(route('api.playlist.forward', ['playlist' => $playlist] + $parameters));

    $response->assertJsonValidationErrors([
        FilterParser::param(),
    ]);
});
