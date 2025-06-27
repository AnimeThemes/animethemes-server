<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\ArtistResource;

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
use App\Http\Api\Schema\Pivot\Wiki\ArtistResourceSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Pivot\Wiki\Collection\ArtistResourceCollection;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistResourceResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\ArtistResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class ArtistResourceIndexTest.
 */
class ArtistResourceIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Artist Resource Index Endpoint shall return a collection of Artist Resource Resources.
     *
     * @return void
     */
    public function test_default(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            ArtistResource::factory()
                ->for(Artist::factory())
                ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
                ->create();
        });

        $artistResources = ArtistResource::all();

        $response = $this->get(route('api.artistresource.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ArtistResourceCollection($artistResources, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Resource Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function test_paginated(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            ArtistResource::factory()
                ->for(Artist::factory())
                ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.artistresource.index'));

        $response->assertJsonStructure([
            ArtistResourceCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Artist Resource Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function test_allowed_include_paths(): void
    {
        $schema = new ArtistResourceSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            ArtistResource::factory()
                ->for(Artist::factory())
                ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.artistresource.index', $parameters));

        $artistResources = ArtistResource::with($includedPaths->all())->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new ArtistResourceCollection($artistResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Resource Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function test_sparse_fieldsets(): void
    {
        $schema = new ArtistResourceSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                ArtistResourceResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            ArtistResource::factory()
                ->for(Artist::factory())
                ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.artistresource.index', $parameters));

        $artistResources = ArtistResource::all();

        $response->assertJson(
            json_decode(
                json_encode(
                    new ArtistResourceCollection($artistResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Resource Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function test_sorts(): void
    {
        $schema = new ArtistResourceSchema();

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
            ArtistResource::factory()
                ->for(Artist::factory())
                ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.artistresource.index', $parameters));

        $artistResources = $this->sort(ArtistResource::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new ArtistResourceCollection($artistResources, $query)
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Resource Index Endpoint shall support filtering by created_at.
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
                ArtistResource::factory()
                    ->for(Artist::factory())
                    ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                ArtistResource::factory()
                    ->for(Artist::factory())
                    ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
                    ->create();
            });
        });

        $artistResources = ArtistResource::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.artistresource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ArtistResourceCollection($artistResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Resource Index Endpoint shall support filtering by updated_at.
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
                ArtistResource::factory()
                    ->for(Artist::factory())
                    ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                ArtistResource::factory()
                    ->for(Artist::factory())
                    ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
                    ->create();
            });
        });

        $artistResources = ArtistResource::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.artistresource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ArtistResourceCollection($artistResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Resource Index Endpoint shall support constrained eager loading of resources by site.
     *
     * @return void
     */
    public function test_resources_by_site(): void
    {
        $siteFilter = Arr::random(ResourceSite::cases());

        $parameters = [
            FilterParser::param() => [
                ExternalResource::ATTRIBUTE_SITE => $siteFilter->localize(),
            ],
            IncludeParser::param() => ArtistResource::RELATION_RESOURCE,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            ArtistResource::factory()
                ->for(Artist::factory())
                ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.artistresource.index', $parameters));

        $artistResources = ArtistResource::with([
            ArtistResource::RELATION_RESOURCE => function (BelongsTo $query) use ($siteFilter) {
                $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new ArtistResourceCollection($artistResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
