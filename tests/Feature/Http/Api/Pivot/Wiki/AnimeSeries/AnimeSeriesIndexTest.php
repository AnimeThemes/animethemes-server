<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\AnimeSeries;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
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
use App\Http\Api\Schema\Pivot\Wiki\AnimeSeriesSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Pivot\Wiki\Collection\AnimeSeriesCollection;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeSeriesResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class AnimeSeriesIndexTest.
 */
class AnimeSeriesIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Anime Series Index Endpoint shall return a collection of Anime Series Resources.
     *
     * @return void
     */
    public function test_default(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeSeries::factory()
                ->for(Anime::factory())
                ->for(Series::factory())
                ->create();
        });

        $animeSeries = AnimeSeries::all();

        $response = $this->get(route('api.animeseries.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeSeriesCollection($animeSeries, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Series Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function test_paginated(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeSeries::factory()
                ->for(Anime::factory())
                ->for(Series::factory())
                ->create();
        });

        $response = $this->get(route('api.animeseries.index'));

        $response->assertJsonStructure([
            AnimeSeriesCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Anime Series Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function test_allowed_include_paths(): void
    {
        $schema = new AnimeSeriesSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeSeries::factory()
                ->for(Anime::factory())
                ->for(Series::factory())
                ->create();
        });

        $response = $this->get(route('api.animeseries.index', $parameters));

        $animeSeries = AnimeSeries::with($includedPaths->all())->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeSeriesCollection($animeSeries, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Series Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function test_sparse_fieldsets(): void
    {
        $schema = new AnimeSeriesSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AnimeSeriesResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeSeries::factory()
                ->for(Anime::factory())
                ->for(Series::factory())
                ->create();
        });

        $response = $this->get(route('api.animeseries.index', $parameters));

        $animeSeries = AnimeSeries::all();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeSeriesCollection($animeSeries, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Series Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function test_sorts(): void
    {
        $schema = new AnimeSeriesSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeSeries::factory()
                ->for(Anime::factory())
                ->for(Series::factory())
                ->create();
        });

        $response = $this->get(route('api.animeseries.index', $parameters));

        $animeSeries = $this->sort(AnimeSeries::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeSeriesCollection($animeSeries, $query)
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Series Index Endpoint shall support filtering by created_at.
     *
     * @return void
     */
    public function test_created_at_filter(): void
    {
        $createdFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BasePivot::ATTRIBUTE_CREATED_AT => $createdFilter,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($createdFilter, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                AnimeSeries::factory()
                    ->for(Anime::factory())
                    ->for(Series::factory())
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                AnimeSeries::factory()
                    ->for(Anime::factory())
                    ->for(Series::factory())
                    ->create();
            });
        });

        $animeSeries = AnimeSeries::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.animeseries.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeSeriesCollection($animeSeries, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Series Index Endpoint shall support filtering by updated_at.
     *
     * @return void
     */
    public function test_updated_at_filter(): void
    {
        $updatedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BasePivot::ATTRIBUTE_UPDATED_AT => $updatedFilter,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($updatedFilter, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                AnimeSeries::factory()
                    ->for(Anime::factory())
                    ->for(Series::factory())
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                AnimeSeries::factory()
                    ->for(Anime::factory())
                    ->for(Series::factory())
                    ->create();
            });
        });

        $animeSeries = AnimeSeries::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.animeseries.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeSeriesCollection($animeSeries, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Series Show Endpoint shall support constrained eager loading of anime by media format.
     *
     * @return void
     */
    public function test_anime_by_media_format(): void
    {
        $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
            ],
            IncludeParser::param() => AnimeSeries::RELATION_ANIME,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeSeries::factory()
                ->for(Anime::factory())
                ->for(Series::factory())
                ->create();
        });

        $response = $this->get(route('api.animeseries.index', $parameters));

        $animeSeries = AnimeSeries::with([
            AnimeSeries::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
                $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeSeriesCollection($animeSeries, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Series Show Endpoint shall support constrained eager loading of anime by season.
     *
     * @return void
     */
    public function test_anime_by_season(): void
    {
        $seasonFilter = Arr::random(AnimeSeason::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
            ],
            IncludeParser::param() => AnimeSeries::RELATION_ANIME,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeSeries::factory()
                ->for(Anime::factory())
                ->for(Series::factory())
                ->create();
        });

        $response = $this->get(route('api.animeseries.index', $parameters));

        $animeSeries = AnimeSeries::with([
            AnimeSeries::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeSeriesCollection($animeSeries, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Series Show Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function test_anime_by_year(): void
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => AnimeSeries::RELATION_ANIME,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () use ($yearFilter, $excludedYear) {
            AnimeSeries::factory()
                ->for(
                    Anime::factory()
                        ->state([
                            Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                        ])
                )
                ->for(Series::factory())
                ->create();
        });

        $response = $this->get(route('api.animeseries.index', $parameters));

        $animeSeries = AnimeSeries::with([
            AnimeSeries::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeSeriesCollection($animeSeries, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
