<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\StudioResource;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\ResourceSite;
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
use App\Http\Api\Schema\Pivot\Wiki\StudioResourceSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Pivot\Wiki\Collection\StudioResourceCollection;
use App\Http\Resources\Pivot\Wiki\Resource\StudioResourceResource;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class StudioResourceIndexTest.
 */
class StudioResourceIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Studio Resource Index Endpoint shall return a collection of Studio Resource Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            StudioResource::factory()
                ->for(Studio::factory())
                ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
                ->create();
        });

        $studioResources = StudioResource::all();

        $response = $this->get(route('api.studioresource.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioResourceCollection($studioResources, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Resource Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            StudioResource::factory()
                ->for(Studio::factory())
                ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.studioresource.index'));

        $response->assertJsonStructure([
            StudioResourceCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Studio Resource Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new StudioResourceSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            StudioResource::factory()
                ->for(Studio::factory())
                ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.studioresource.index', $parameters));

        $studioResources = StudioResource::with($includedPaths->all())->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioResourceCollection($studioResources, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Resource Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new StudioResourceSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                StudioResourceResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            StudioResource::factory()
                ->for(Studio::factory())
                ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.studioresource.index', $parameters));

        $studioResources = StudioResource::all();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioResourceCollection($studioResources, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Resource Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new StudioResourceSchema();

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
            StudioResource::factory()
                ->for(Studio::factory())
                ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.studioresource.index', $parameters));

        $studioResources = $this->sort(StudioResource::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioResourceCollection($studioResources, $query))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Resource Index Endpoint shall support filtering by created_at.
     *
     * @return void
     */
    public function testCreatedAtFilter(): void
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
                StudioResource::factory()
                    ->for(Studio::factory())
                    ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                StudioResource::factory()
                    ->for(Studio::factory())
                    ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
                    ->create();
            });
        });

        $studioResources = StudioResource::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.studioresource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioResourceCollection($studioResources, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Resource Index Endpoint shall support filtering by updated_at.
     *
     * @return void
     */
    public function testUpdatedAtFilter(): void
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
                StudioResource::factory()
                    ->for(Studio::factory())
                    ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                StudioResource::factory()
                    ->for(Studio::factory())
                    ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
                    ->create();
            });
        });

        $studioResources = StudioResource::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.studioresource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioResourceCollection($studioResources, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Resource Index Endpoint shall support constrained eager loading of resources by site.
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
            IncludeParser::param() => StudioResource::RELATION_RESOURCE,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            StudioResource::factory()
                ->for(Studio::factory())
                ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.studioresource.index', $parameters));

        $studioResources = StudioResource::with([
            StudioResource::RELATION_RESOURCE => function (BelongsTo $query) use ($siteFilter) {
                $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new StudioResourceCollection($studioResources, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
