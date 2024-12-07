<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\SongResource;

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
use App\Http\Api\Schema\Pivot\Wiki\SongResourceSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Pivot\Wiki\Collection\SongResourceCollection;
use App\Http\Resources\Pivot\Wiki\Resource\SongResourceResource;
use App\Models\Wiki\Song;
use App\Models\Wiki\ExternalResource;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\SongResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class SongResourceIndexTest.
 */
class SongResourceIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Song Resource Index Endpoint shall return a collection of Song Resource Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            SongResource::factory()
                ->for(Song::factory())
                ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
                ->create();
        });

        $songResources = SongResource::all();

        $response = $this->get(route('api.songresource.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SongResourceCollection($songResources, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Resource Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            SongResource::factory()
                ->for(Song::factory())
                ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.songresource.index'));

        $response->assertJsonStructure([
            SongResourceCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Song Resource Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new SongResourceSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            SongResource::factory()
                ->for(Song::factory())
                ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.songresource.index', $parameters));

        $songResources = SongResource::with($includedPaths->all())->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new SongResourceCollection($songResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Resource Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new SongResourceSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                SongResourceResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            SongResource::factory()
                ->for(Song::factory())
                ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.songresource.index', $parameters));

        $songResources = SongResource::all();

        $response->assertJson(
            json_decode(
                json_encode(
                    new SongResourceCollection($songResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Resource Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new SongResourceSchema();

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
            SongResource::factory()
                ->for(Song::factory())
                ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.songresource.index', $parameters));

        $songResources = $this->sort(SongResource::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new SongResourceCollection($songResources, $query)
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Resource Index Endpoint shall support filtering by created_at.
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
                SongResource::factory()
                    ->for(Song::factory())
                    ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                SongResource::factory()
                    ->for(Song::factory())
                    ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
                    ->create();
            });
        });

        $songResources = SongResource::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.songresource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SongResourceCollection($songResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Resource Index Endpoint shall support filtering by updated_at.
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
                SongResource::factory()
                    ->for(Song::factory())
                    ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                SongResource::factory()
                    ->for(Song::factory())
                    ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
                    ->create();
            });
        });

        $songResources = SongResource::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.songresource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SongResourceCollection($songResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Resource Index Endpoint shall support constrained eager loading of resources by site.
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
            IncludeParser::param() => SongResource::RELATION_RESOURCE,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            SongResource::factory()
                ->for(Song::factory())
                ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.songresource.index', $parameters));

        $songResources = SongResource::with([
            SongResource::RELATION_RESOURCE => function (BelongsTo $query) use ($siteFilter) {
                $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new SongResourceCollection($songResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
