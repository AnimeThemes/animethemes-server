<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\AnimeResource;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
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
use App\Http\Api\Schema\Pivot\Wiki\AnimeResourceSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Pivot\Wiki\Collection\AnimeResourceCollection;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeResourceResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\AnimeResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class AnimeResourceIndexTest.
 */
class AnimeResourceIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Anime Resource Index Endpoint shall return a collection of Anime Resource Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeResource::factory()
                ->for(Anime::factory())
                ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
                ->create();
        });

        $animeResources = AnimeResource::all();

        $response = $this->get(route('api.animeresource.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeResourceCollection($animeResources, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * Anime Resource Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeResource::factory()
                ->for(Anime::factory())
                ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.animeresource.index'));

        $response->assertJsonStructure([
            AnimeResourceCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Anime Resource Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new AnimeResourceSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeResource::factory()
                ->for(Anime::factory())
                ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.animeresource.index', $parameters));

        $animeResources = AnimeResource::with($includedPaths->all())->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeResourceCollection($animeResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Resource Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new AnimeResourceSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AnimeResourceResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeResource::factory()
                ->for(Anime::factory())
                ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.animeresource.index', $parameters));

        $animeResources = AnimeResource::all();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeResourceCollection($animeResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Resource Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new AnimeResourceSchema();

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
            AnimeResource::factory()
                ->for(Anime::factory())
                ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.animeresource.index', $parameters));

        $animeResources = $this->sort(AnimeResource::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeResourceCollection($animeResources, $query)
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Resource Index Endpoint shall support filtering by created_at.
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
                AnimeResource::factory()
                    ->for(Anime::factory())
                    ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                AnimeResource::factory()
                    ->for(Anime::factory())
                    ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
                    ->create();
            });
        });

        $animeResources = AnimeResource::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.animeresource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeResourceCollection($animeResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Resource Index Endpoint shall support filtering by updated_at.
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
                AnimeResource::factory()
                    ->for(Anime::factory())
                    ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                AnimeResource::factory()
                    ->for(Anime::factory())
                    ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
                    ->create();
            });
        });

        $animeResources = AnimeResource::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.animeresource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeResourceCollection($animeResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Resource Index Endpoint shall support constrained eager loading of resources by site.
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
            IncludeParser::param() => AnimeResource::RELATION_RESOURCE,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeResource::factory()
                ->for(Anime::factory())
                ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.animeresource.index', $parameters));

        $animeResources = AnimeResource::with([
            AnimeResource::RELATION_RESOURCE => function (BelongsTo $query) use ($siteFilter) {
                $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeResourceCollection($animeResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Resource Show Endpoint shall support constrained eager loading of anime by media format.
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
            IncludeParser::param() => AnimeResource::RELATION_ANIME,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeResource::factory()
                ->for(Anime::factory())
                ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.animeresource.index', $parameters));

        $animeResources = AnimeResource::with([
            AnimeResource::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
                $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeResourceCollection($animeResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Resource Show Endpoint shall support constrained eager loading of anime by season.
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
            IncludeParser::param() => AnimeResource::RELATION_ANIME,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeResource::factory()
                ->for(Anime::factory())
                ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.animeresource.index', $parameters));

        $animeResources = AnimeResource::with([
            AnimeResource::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeResourceCollection($animeResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Resource Show Endpoint shall support constrained eager loading of anime by year.
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
            IncludeParser::param() => AnimeResource::RELATION_ANIME,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () use ($yearFilter, $excludedYear) {
            AnimeResource::factory()
                ->for(
                    Anime::factory()
                        ->state([
                            Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                        ])
                )
                ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
                ->create();
        });

        $response = $this->get(route('api.animeresource.index', $parameters));

        $animeResources = AnimeResource::with([
            AnimeResource::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeResourceCollection($animeResources, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
