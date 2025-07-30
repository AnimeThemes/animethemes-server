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
use App\Http\Api\Schema\List\Playlist\TrackSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\List\Playlist\Collection\TrackCollection;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('private playlist track cannot be publicly viewed', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
        ]);

    $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist]));

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

    $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist]));

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

    $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist]));

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

    $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist]));

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

    $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist]));

    $response->assertOk();
});

test('default', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $trackCount = fake()->randomDigitNotNull();

    $playlist = Playlist::factory()
        ->has(PlaylistTrack::factory()->count($trackCount), Playlist::RELATION_TRACKS)
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

    $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist]));

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
        ->has(PlaylistTrack::factory()->count(fake()->randomDigitNotNull()), Playlist::RELATION_TRACKS)
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist]));

    $response->assertJsonStructure([
        TrackCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $schema = new TrackSchema();

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
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    PlaylistTrack::factory()
        ->for($playlist)
        ->for(Video::factory())
        ->for(PlaylistTrack::factory()->for($playlist), PlaylistTrack::RELATION_PREVIOUS)
        ->for(PlaylistTrack::factory()->for($playlist), PlaylistTrack::RELATION_NEXT)
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist] + $parameters));

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

    $schema = new TrackSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            TrackResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $playlist = Playlist::factory()
        ->has(PlaylistTrack::factory()->count(fake()->randomDigitNotNull()), Playlist::RELATION_TRACKS)
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist] + $parameters));

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

    $schema = new TrackSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $playlist = Playlist::factory()
        ->has(PlaylistTrack::factory()->count(fake()->randomDigitNotNull()), Playlist::RELATION_TRACKS)
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $query = new Query($parameters);

    $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist] + $parameters));

    $tracks = $this->sort(PlaylistTrack::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new TrackCollection($tracks, $query)
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('created at filter', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $createdFilter = fake()->date();
    $excludedDate = fake()->date();

    $parameters = [
        FilterParser::param() => [
            BaseModel::ATTRIBUTE_CREATED_AT => $createdFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    $playlist = Playlist::factory()
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    Carbon::withTestNow(
        $createdFilter,
        fn () => PlaylistTrack::factory()
            ->for($playlist)
            ->count(fake()->randomDigitNotNull())
            ->create()
    );

    Carbon::withTestNow(
        $excludedDate,
        fn () => PlaylistTrack::factory()
            ->for($playlist)
            ->count(fake()->randomDigitNotNull())
            ->create()
    );

    $tracks = PlaylistTrack::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist] + $parameters));

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

test('updated at filter', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $updatedFilter = fake()->date();
    $excludedDate = fake()->date();

    $parameters = [
        FilterParser::param() => [
            BaseModel::ATTRIBUTE_UPDATED_AT => $updatedFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    $playlist = Playlist::factory()
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    Carbon::withTestNow(
        $updatedFilter,
        fn () => PlaylistTrack::factory()
            ->for($playlist)
            ->count(fake()->randomDigitNotNull())
            ->create()
    );

    Carbon::withTestNow(
        $excludedDate,
        fn () => PlaylistTrack::factory()
            ->for($playlist)
            ->count(fake()->randomDigitNotNull())
            ->create()
    );

    $tracks = PlaylistTrack::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist] + $parameters));

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
