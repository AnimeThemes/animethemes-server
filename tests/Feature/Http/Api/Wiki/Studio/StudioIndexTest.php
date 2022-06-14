<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Studio;

use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeSeason;
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
use App\Http\Api\Query\Wiki\Studio\StudioReadQuery;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Resources\Wiki\Collection\StudioCollection;
use App\Http\Resources\Wiki\Resource\StudioResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class StudioIndexTest.
 */
class StudioIndexTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

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
                    (new StudioCollection($studio, new StudioReadQuery()))
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
                    (new StudioCollection($studio, new StudioReadQuery($parameters)))
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
                    (new StudioCollection($studio, new StudioReadQuery($parameters)))
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

        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Direction::getRandomInstance()),
        ];

        $query = new StudioReadQuery($parameters);

        Studio::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    $query->collection($query->index())
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
                    (new StudioCollection($studio, new StudioReadQuery($parameters)))
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
                    (new StudioCollection($studio, new StudioReadQuery($parameters)))
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
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Studio::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteStudio = Studio::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteStudio->each(function (Studio $studio) {
            $studio->delete();
        });

        $studio = Studio::withoutTrashed()->get();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studio, new StudioReadQuery($parameters)))
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
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Studio::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteStudio = Studio::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteStudio->each(function (Studio $studio) {
            $studio->delete();
        });

        $studio = Studio::withTrashed()->get();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studio, new StudioReadQuery($parameters)))
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
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Studio::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteStudio = Studio::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteStudio->each(function (Studio $studio) {
            $studio->delete();
        });

        $studio = Studio::onlyTrashed()->get();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studio, new StudioReadQuery($parameters)))
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
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            $studio = Studio::factory()->count($this->faker->randomDigitNotNull())->create();
            $studio->each(function (Studio $item) {
                $item->delete();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            $studio = Studio::factory()->count($this->faker->randomDigitNotNull())->create();
            $studio->each(function (Studio $item) {
                $item->delete();
            });
        });

        $studio = Studio::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.studio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioCollection($studio, new StudioReadQuery($parameters)))
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
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->description,
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
                    (new StudioCollection($studio, new StudioReadQuery($parameters)))
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
                    (new StudioCollection($studio, new StudioReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
