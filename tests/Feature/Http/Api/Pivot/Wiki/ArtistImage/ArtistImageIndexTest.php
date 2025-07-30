<?php

declare(strict_types=1);

use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
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
use App\Http\Api\Schema\Pivot\Wiki\ArtistImageSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Pivot\Wiki\Collection\ArtistImageCollection;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistImageResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\ArtistImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    Collection::times(fake()->randomDigitNotNull(), function () {
        ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->create();
    });

    $artistImages = ArtistImage::all();

    $response = $this->get(route('api.artistimage.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistImageCollection($artistImages, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function () {
    Collection::times(fake()->randomDigitNotNull(), function () {
        ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->create();
    });

    $response = $this->get(route('api.artistimage.index'));

    $response->assertJsonStructure([
        ArtistImageCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    $schema = new ArtistImageSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
        ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->create();
    });

    $response = $this->get(route('api.artistimage.index', $parameters));

    $artistImages = ArtistImage::with($includedPaths->all())->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistImageCollection($artistImages, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new ArtistImageSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ArtistImageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
        ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->create();
    });

    $response = $this->get(route('api.artistimage.index', $parameters));

    $artistImages = ArtistImage::all();

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistImageCollection($artistImages, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function () {
    $schema = new ArtistImageSchema();

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
        ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->create();
    });

    $response = $this->get(route('api.artistimage.index', $parameters));

    $artistImages = $this->sort(ArtistImage::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistImageCollection($artistImages, $query)
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
            ArtistImage::factory()
                ->for(Artist::factory())
                ->for(Image::factory())
                ->create();
        });
    });

    Carbon::withTestNow($excludedDate, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
            ArtistImage::factory()
                ->for(Artist::factory())
                ->for(Image::factory())
                ->create();
        });
    });

    $artistImages = ArtistImage::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = $this->get(route('api.artistimage.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistImageCollection($artistImages, new Query($parameters))
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
            ArtistImage::factory()
                ->for(Artist::factory())
                ->for(Image::factory())
                ->create();
        });
    });

    Carbon::withTestNow($excludedDate, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
            ArtistImage::factory()
                ->for(Artist::factory())
                ->for(Image::factory())
                ->create();
        });
    });

    $artistImages = ArtistImage::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = $this->get(route('api.artistimage.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistImageCollection($artistImages, new Query($parameters))
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
        IncludeParser::param() => ArtistImage::RELATION_IMAGE,
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
        ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->create();
    });

    $response = $this->get(route('api.artistimage.index', $parameters));

    $artistImages = ArtistImage::with([
        ArtistImage::RELATION_IMAGE => function (BelongsTo $query) use ($facetFilter) {
            $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
        },
    ])
        ->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistImageCollection($artistImages, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
