<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\ExternalResource;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
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
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Class ExternalResourceIndexTest.
 */
class ExternalResourceIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Resource Index Endpoint shall return a collection of ExternalResource Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $resources = ExternalResource::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.resource.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalResourceCollection($resources, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        ExternalResource::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.resource.index'));

        $response->assertJsonStructure([
            ExternalResourceCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Resource Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new ExternalResourceSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $resources = ExternalResource::with($includedPaths->all())->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalResourceCollection($resources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new ExternalResourceSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                ExternalResourceResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $resources = ExternalResource::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalResourceCollection($resources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new ExternalResourceSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        ExternalResource::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.resource.index', $parameters));

        $resources = $this->sort(ExternalResource::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalResourceCollection($resources, $query)
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support filtering by created_at.
     *
     * @return void
     */
    public function testCreatedAtFilter(): void
    {
        $createdFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_CREATED_AT => $createdFilter,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($createdFilter, function () {
            ExternalResource::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            ExternalResource::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $resource = ExternalResource::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalResourceCollection($resource, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support filtering by updated_at.
     *
     * @return void
     */
    public function testUpdatedAtFilter(): void
    {
        $updatedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_UPDATED_AT => $updatedFilter,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($updatedFilter, function () {
            ExternalResource::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            ExternalResource::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $resource = ExternalResource::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalResourceCollection($resource, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        ExternalResource::factory()->count($this->faker->randomDigitNotNull())->create();

        ExternalResource::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $resource = ExternalResource::withoutTrashed()->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalResourceCollection($resource, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        ExternalResource::factory()->count($this->faker->randomDigitNotNull())->create();

        ExternalResource::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $resource = ExternalResource::withTrashed()->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalResourceCollection($resource, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        ExternalResource::factory()->count($this->faker->randomDigitNotNull())->create();

        ExternalResource::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $resource = ExternalResource::onlyTrashed()->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalResourceCollection($resource, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support filtering by deleted_at.
     *
     * @return void
     */
    public function testDeletedAtFilter(): void
    {
        $deletedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_DELETED_AT => $deletedFilter,
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            ExternalResource::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            ExternalResource::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        $resource = ExternalResource::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalResourceCollection($resource, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support filtering by site.
     *
     * @return void
     */
    public function testSiteFilter(): void
    {
        $siteFilter = Arr::random(ResourceSite::cases());

        $parameters = [
            FilterParser::param() => [
                ExternalResource::ATTRIBUTE_SITE => $siteFilter->localize(),
            ],
        ];

        ExternalResource::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $resources = ExternalResource::query()->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value)->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalResourceCollection($resources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support constrained eager loading of anime by media format.
     *
     * @return void
     */
    public function testAnimeByMediaFormat(): void
    {
        $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
            ],
            IncludeParser::param() => ExternalResource::RELATION_ANIME,
        ];

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $resources = ExternalResource::with([
            ExternalResource::RELATION_ANIME => function (BelongsToMany $query) use ($mediaFormatFilter) {
                $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalResourceCollection($resources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support constrained eager loading of anime by season.
     *
     * @return void
     */
    public function testAnimeBySeason(): void
    {
        $seasonFilter = Arr::random(AnimeSeason::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
            ],
            IncludeParser::param() => ExternalResource::RELATION_ANIME,
        ];

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $resources = ExternalResource::with([
            ExternalResource::RELATION_ANIME => function (BelongsToMany $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalResourceCollection($resources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function testAnimeByYear(): void
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => ExternalResource::RELATION_ANIME,
        ];

        ExternalResource::factory()
            ->has(
                Anime::factory()
                ->count($this->faker->randomDigitNotNull())
                ->state([
                    Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                ])
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $resources = ExternalResource::with([
            ExternalResource::RELATION_ANIME => function (BelongsToMany $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalResourceCollection($resources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
