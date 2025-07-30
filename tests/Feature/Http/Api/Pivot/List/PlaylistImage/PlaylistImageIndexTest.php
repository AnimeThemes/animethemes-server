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
use App\Http\Api\Schema\Pivot\List\PlaylistImageSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Pivot\List\Collection\PlaylistImageCollection;
use App\Http\Resources\Pivot\List\Resource\PlaylistImageResource;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Pivots\BasePivot;
use App\Pivots\List\PlaylistImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $publicCount = fake()->randomDigitNotNull();

    Collection::times($publicCount, function () {
        PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for(User::factory())
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
                    ])
            )
            ->for(Image::factory())
            ->create();
    });

    Collection::times(fake()->randomDigitNotNull(), function () {
        PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for(User::factory())
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::UNLISTED->value,
                    ])
            )
            ->for(Image::factory())
            ->create();
    });

    Collection::times(fake()->randomDigitNotNull(), function () {
        PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for(User::factory())
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
                    ])
            )
            ->for(Image::factory())
            ->create();
    });

    $playlistImages = PlaylistImage::query()
        ->whereHas(PlaylistImage::RELATION_PLAYLIST, function (Builder $relationBuilder) {
            $relationBuilder->where(Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::PUBLIC->value);
        })
        ->get();

    $response = $this->get(route('api.playlistimage.index'));

    $response->assertJsonCount($publicCount, PlaylistImageCollection::$wrap);

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistImageCollection($playlistImages, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function () {
    Collection::times(fake()->randomDigitNotNull(), function () {
        PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for(User::factory())
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
                    ])
            )
            ->for(Image::factory())
            ->create();
    });

    $response = $this->get(route('api.playlistimage.index'));

    $response->assertJsonStructure([
        PlaylistImageCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    $schema = new PlaylistImageSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
        PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for(User::factory())
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
                    ])
            )
            ->for(Image::factory())
            ->create();
    });

    $response = $this->get(route('api.playlistimage.index', $parameters));

    $playlistImages = PlaylistImage::with($includedPaths->all())->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistImageCollection($playlistImages, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new PlaylistImageSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            PlaylistImageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
        PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for(User::factory())
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
                    ])
            )
            ->for(Image::factory())
            ->create();
    });

    $response = $this->get(route('api.playlistimage.index', $parameters));

    $playlistImages = PlaylistImage::all();

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistImageCollection($playlistImages, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function () {
    $schema = new PlaylistImageSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Collection::times(fake()->randomDigitNotNull(), function () {
        PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for(User::factory())
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
                    ])
            )
            ->for(Image::factory())
            ->create();
    });

    $response = $this->get(route('api.playlistimage.index', $parameters));

    $playlistImages = $this->sort(PlaylistImage::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistImageCollection($playlistImages, $query)
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
            BasePivot::ATTRIBUTE_CREATED_AT => $createdFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Carbon::withTestNow($createdFilter, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
            PlaylistImage::factory()
                ->for(
                    Playlist::factory()
                        ->for(User::factory())
                        ->state([
                            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
                        ])
                )
                ->for(Image::factory())
                ->create();
        });
    });

    Carbon::withTestNow($excludedDate, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
            PlaylistImage::factory()
                ->for(
                    Playlist::factory()
                        ->for(User::factory())
                        ->state([
                            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
                        ])
                )
                ->for(Image::factory())
                ->create();
        });
    });

    $playlistImages = PlaylistImage::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = $this->get(route('api.playlistimage.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistImageCollection($playlistImages, new Query($parameters))
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
            BasePivot::ATTRIBUTE_UPDATED_AT => $updatedFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Carbon::withTestNow($updatedFilter, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
            PlaylistImage::factory()
                ->for(
                    Playlist::factory()
                        ->for(User::factory())
                        ->state([
                            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
                        ])
                )
                ->for(Image::factory())
                ->create();
        });
    });

    Carbon::withTestNow($excludedDate, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
            PlaylistImage::factory()
                ->for(
                    Playlist::factory()
                        ->for(User::factory())
                        ->state([
                            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
                        ])
                )
                ->for(Image::factory())
                ->create();
        });
    });

    $playlistImages = PlaylistImage::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = $this->get(route('api.playlistimage.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistImageCollection($playlistImages, new Query($parameters))
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
        IncludeParser::param() => PlaylistImage::RELATION_IMAGE,
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
        PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for(User::factory())
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
                    ])
            )
            ->for(Image::factory())
            ->create();
    });

    $response = $this->get(route('api.playlistimage.index', $parameters));

    $playlistImages = PlaylistImage::with([
        PlaylistImage::RELATION_IMAGE => function (BelongsTo $query) use ($facetFilter) {
            $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
        },
    ])
        ->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistImageCollection($playlistImages, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
