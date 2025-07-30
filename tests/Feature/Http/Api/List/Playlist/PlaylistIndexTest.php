<?php

declare(strict_types=1);

use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Models\Wiki\ImageFacet;
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
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\List\Collection\PlaylistCollection;
use App\Http\Resources\List\Resource\PlaylistResource;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

uses(App\Concerns\Actions\Http\Api\AggregatesFields::class);

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $publicCount = fake()->randomDigitNotNull();

    $playlists = Playlist::factory()
        ->count($publicCount)
        ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value]);

    $unlistedCount = fake()->randomDigitNotNull();

    Playlist::factory()
        ->count($unlistedCount)
        ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::UNLISTED->value]);

    $privateCount = fake()->randomDigitNotNull();

    Playlist::factory()
        ->count($privateCount)
        ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value]);

    $response = $this->get(route('api.playlist.index'));

    $response->assertJsonCount($publicCount, PlaylistCollection::$wrap);

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistCollection($playlists, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function () {
    Playlist::factory()
        ->count(fake()->randomDigitNotNull())
        ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value]);

    $response = $this->get(route('api.playlist.index'));

    $response->assertJsonStructure([
        PlaylistCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    $schema = new PlaylistSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Playlist::factory()
        ->for(User::factory())
        ->has(PlaylistTrack::factory(), Playlist::RELATION_FIRST)
        ->has(PlaylistTrack::factory(), Playlist::RELATION_LAST)
        ->has(PlaylistTrack::factory()->count(fake()->randomDigitNotNull()), Playlist::RELATION_TRACKS)
        ->has(Image::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $playlists = Playlist::with($includedPaths->all())->get();

    $response = $this->get(route('api.playlist.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistCollection($playlists, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new PlaylistSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            PlaylistResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $playlists = Playlist::factory()
        ->count(fake()->randomDigitNotNull())
        ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value]);

    $response = $this->get(route('api.playlist.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistCollection($playlists, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function () {
    $schema = new PlaylistSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Playlist::factory()
        ->count(fake()->randomDigitNotNull())
        ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value]);

    $response = $this->get(route('api.playlist.index', $parameters));

    $builder = Playlist::query();
    $this->withAggregates($builder, $query, $schema);
    $playlists = $this->sort($builder, $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistCollection($playlists, $query)
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('created at filter', function () {
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

    Carbon::withTestNow($createdFilter, function () {
        Playlist::factory()
            ->count(fake()->randomDigitNotNull())
            ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value]);
    });

    Carbon::withTestNow($excludedDate, function () {
        Playlist::factory()
            ->count(fake()->randomDigitNotNull())
            ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value]);
    });

    $playlists = Playlist::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = $this->get(route('api.playlist.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistCollection($playlists, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('updated at filter', function () {
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

    Carbon::withTestNow($updatedFilter, function () {
        Playlist::factory()
            ->count(fake()->randomDigitNotNull())
            ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value]);
    });

    Carbon::withTestNow($excludedDate, function () {
        Playlist::factory()
            ->count(fake()->randomDigitNotNull())
            ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value]);
    });

    $playlists = Playlist::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = $this->get(route('api.playlist.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistCollection($playlists, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('images by facet', function () {
    $facetFilter = Arr::random(ImageFacet::cases());

    $parameters = [
        FilterParser::param() => [
            Image::ATTRIBUTE_FACET => $facetFilter->localize(),
        ],
        IncludeParser::param() => Playlist::RELATION_IMAGES,
    ];

    Playlist::factory()
        ->has(Image::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $playlists = Playlist::with([
        Playlist::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
            $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
        },
    ])
        ->get();

    $response = $this->get(route('api.playlist.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistCollection($playlists, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
