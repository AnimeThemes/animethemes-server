<?php

declare(strict_types=1);

use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
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
use App\Http\Api\Schema\Admin\FeaturedThemeSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Admin\Collection\FeaturedThemeCollection;
use App\Http\Resources\Admin\Resource\FeaturedThemeResource;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Models\Wiki\Video;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use function Pest\Laravel\get;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $publicCount = fake()->randomDigitNotNull();

    $featuredThemes = FeaturedTheme::factory()->count($publicCount)->create();

    Collection::times(fake()->randomDigitNotNull(), function () {
        FeaturedTheme::factory()->future();
    });

    $response = get(route('api.featuredtheme.index'));

    $response->assertJsonCount($publicCount, FeaturedThemeCollection::$wrap);

    $response->assertJson(
        json_decode(
            json_encode(
                new FeaturedThemeCollection($featuredThemes, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function () {
    FeaturedTheme::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.featuredtheme.index'));

    $response->assertJsonStructure([
        FeaturedThemeCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    $schema = new FeaturedThemeSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    FeaturedTheme::factory()
        ->for(
            AnimeThemeEntry::factory()
                ->for(
                    AnimeTheme::factory()
                        ->for(Anime::factory()->has(Image::factory()->count(fake()->randomDigitNotNull())))
                        ->for(Song::factory()->has(Artist::factory()->count(fake()->randomDigitNotNull())))
                )
        )
        ->for(Video::factory())
        ->for(User::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $featuredThemes = FeaturedTheme::with($includedPaths->all())->get();

    $response = get(route('api.featuredtheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeaturedThemeCollection($featuredThemes, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new FeaturedThemeSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            FeaturedThemeResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $featuredThemes = FeaturedTheme::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.featuredtheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeaturedThemeCollection($featuredThemes, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function () {
    $schema = new FeaturedThemeSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    FeaturedTheme::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.featuredtheme.index', $parameters));

    $featuredThemes = $this->sort(FeaturedTheme::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new FeaturedThemeCollection($featuredThemes, $query)
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
        FeaturedTheme::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        FeaturedTheme::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $featuredTheme = FeaturedTheme::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.featuredtheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeaturedThemeCollection($featuredTheme, new Query($parameters))
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
        FeaturedTheme::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        FeaturedTheme::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $featuredTheme = FeaturedTheme::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.featuredtheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeaturedThemeCollection($featuredTheme, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
