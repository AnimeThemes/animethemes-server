<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\AnimeStudio;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
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
use App\Http\Api\Schema\Pivot\Wiki\AnimeStudioSchema;
use App\Http\Resources\Pivot\Wiki\Collection\AnimeStudioCollection;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeStudioResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class AnimeStudioIndexTest.
 */
class AnimeStudioIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Anime Studio Index Endpoint shall return a collection of Anime Studio Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeStudio::factory()
                ->for(Anime::factory())
                ->for(Studio::factory())
                ->create();
        });

        $animeStudios = AnimeStudio::all();

        $response = $this->get(route('api.animestudio.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeStudioCollection($animeStudios, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeStudio::factory()
                ->for(Anime::factory())
                ->for(Studio::factory())
                ->create();
        });

        $response = $this->get(route('api.animestudio.index'));

        $response->assertJsonStructure([
            AnimeStudioCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Anime Studio Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new AnimeStudioSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeStudio::factory()
                ->for(Anime::factory())
                ->for(Studio::factory())
                ->create();
        });

        $response = $this->get(route('api.animestudio.index', $parameters));

        $animeStudios = AnimeStudio::with($includedPaths->all())->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeStudioCollection($animeStudios, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Studio Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new AnimeStudioSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AnimeStudioResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeStudio::factory()
                ->for(Anime::factory())
                ->for(Studio::factory())
                ->create();
        });

        $response = $this->get(route('api.animestudio.index', $parameters));

        $animeStudios = AnimeStudio::all();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeStudioCollection($animeStudios, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Studio Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new AnimeStudioSchema();

        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Direction::getRandomInstance()),
        ];

        $query = new Query($parameters);

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeStudio::factory()
                ->for(Anime::factory())
                ->for(Studio::factory())
                ->create();
        });

        $response = $this->get(route('api.animestudio.index', $parameters));

        $animeStudios = $this->sort(AnimeStudio::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeStudioCollection($animeStudios, $query))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Studio Index Endpoint shall support filtering by created_at.
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
                AnimeStudio::factory()
                    ->for(Anime::factory())
                    ->for(Studio::factory())
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                AnimeStudio::factory()
                    ->for(Anime::factory())
                    ->for(Studio::factory())
                    ->create();
            });
        });

        $animeStudios = AnimeStudio::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.animestudio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeStudioCollection($animeStudios, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Studio Index Endpoint shall support filtering by updated_at.
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
                AnimeStudio::factory()
                    ->for(Anime::factory())
                    ->for(Studio::factory())
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                AnimeStudio::factory()
                    ->for(Anime::factory())
                    ->for(Studio::factory())
                    ->create();
            });
        });

        $animeStudios = AnimeStudio::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.animestudio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeStudioCollection($animeStudios, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Studio Show Endpoint shall support constrained eager loading of anime by season.
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
            IncludeParser::param() => AnimeStudio::RELATION_ANIME,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeStudio::factory()
                ->for(Anime::factory())
                ->for(Studio::factory())
                ->create();
        });

        $response = $this->get(route('api.animestudio.index', $parameters));

        $animeStudios = AnimeStudio::with([
            AnimeStudio::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeStudioCollection($animeStudios, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Studio Show Endpoint shall support constrained eager loading of anime by year.
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
            IncludeParser::param() => AnimeStudio::RELATION_ANIME,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () use ($yearFilter, $excludedYear) {
            AnimeStudio::factory()
                ->for(
                    Anime::factory()
                        ->state([
                            Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                        ])
                )
                ->for(Studio::factory())
                ->create();
        });

        $response = $this->get(route('api.animestudio.index', $parameters));

        $animeStudios = AnimeStudio::with([
            AnimeStudio::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeStudioCollection($animeStudios, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}