<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Studio;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
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
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Collection\StudioCollection;
use App\Http\Resources\Wiki\Resource\StudioResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Class StudioIndexTest.
 */
class StudioIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Studio Index Endpoint shall return a collection of Studio Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $studio = Studio::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.studio.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studio, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        Studio::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.studio.index'));

        $response->assertJsonStructure([
            StudioCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Studio Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new StudioSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Studio::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $studio = Studio::with($includedPaths->all())->get();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studio, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new StudioSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                StudioResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $studio = Studio::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studio, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new StudioSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        Studio::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.studio.index', $parameters));

        $studios = $this->sort(Studio::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studios, $query))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Index Endpoint shall support filtering by created_at.
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
            Studio::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Studio::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $studio = Studio::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studio, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Index Endpoint shall support filtering by updated_at.
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
            Studio::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Studio::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $studio = Studio::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studio, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Index Endpoint shall support filtering by trashed.
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

        Studio::factory()->count($this->faker->randomDigitNotNull())->create();

        Studio::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $studio = Studio::withoutTrashed()->get();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studio, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Index Endpoint shall support filtering by trashed.
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

        Studio::factory()->count($this->faker->randomDigitNotNull())->create();

        Studio::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $studio = Studio::withTrashed()->get();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studio, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Index Endpoint shall support filtering by trashed.
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

        Studio::factory()->count($this->faker->randomDigitNotNull())->create();

        Studio::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $studio = Studio::onlyTrashed()->get();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studio, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Index Endpoint shall support filtering by deleted_at.
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
            Studio::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Studio::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        $studio = Studio::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studio, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Index Endpoint shall support constrained eager loading of anime by media format.
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
            IncludeParser::param() => Studio::RELATION_ANIME,
        ];

        Studio::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $studio = Studio::with([
            Studio::RELATION_ANIME => function (BelongsToMany $query) use ($mediaFormatFilter) {
                $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studio, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Index Endpoint shall support constrained eager loading of anime by season.
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
            IncludeParser::param() => Studio::RELATION_ANIME,
        ];

        Studio::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $studio = Studio::with([
            Studio::RELATION_ANIME => function (BelongsToMany $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studio, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Index Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function testAnimeByYear(): void
    {
        $yearFilter = $this->faker->numberBetween(2000, 2002);

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => Studio::RELATION_ANIME,
        ];

        Studio::factory()
            ->has(
                Anime::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        [Anime::ATTRIBUTE_YEAR => 2000],
                        [Anime::ATTRIBUTE_YEAR => 2001],
                        [Anime::ATTRIBUTE_YEAR => 2002],
                    ))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $studio = Studio::with([
            Studio::RELATION_ANIME => function (BelongsToMany $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studio, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Index Endpoint shall support constrained eager loading of resources by site.
     *
     * @return void
     */
    public function testResourcesBySite(): void
    {
        $siteFilter = Arr::random(ResourceSite::cases());

        $parameters = [
            FilterParser::param() => [
                ExternalResource::ATTRIBUTE_SITE => $siteFilter->localize(),
            ],
            IncludeParser::param() => Studio::RELATION_RESOURCES,
        ];

        Studio::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull()), Studio::RELATION_RESOURCES)
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $studios = Studio::with([
            Studio::RELATION_RESOURCES => function (BelongsToMany $query) use ($siteFilter) {
                $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
            },
        ])
            ->get();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studios, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Index Endpoint shall support constrained eager loading of images by facet.
     *
     * @return void
     */
    public function testImagesByFacet(): void
    {
        $facetFilter = Arr::random(ImageFacet::cases());

        $parameters = [
            FilterParser::param() => [
                Image::ATTRIBUTE_FACET => $facetFilter->localize(),
            ],
            IncludeParser::param() => Studio::RELATION_IMAGES,
        ];

        Studio::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $anime = Studio::with([
            Studio::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
            },
        ])
            ->get();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($anime, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
