<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Series;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\SeriesSchema;
use App\Http\Resources\Wiki\Resource\SeriesResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class SeriesShowTest.
 */
class SeriesShowTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Series Show Endpoint shall return a Series Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $series = Series::factory()->create();

        $response = $this->get(route('api.series.show', ['series' => $series]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new SeriesResource($series, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall return a Series Resource for soft deleted series.
     *
     * @return void
     */
    public function testSoftDelete(): void
    {
        $series = Series::factory()->trashed()->createOne();

        $series->unsetRelations();

        $response = $this->get(route('api.series.show', ['series' => $series]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new SeriesResource($series, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new SeriesSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $series = Series::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->createOne();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new SeriesResource($series, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new SeriesSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                SeriesResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $series = Series::factory()->create();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new SeriesResource($series, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of anime by season.
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
            IncludeParser::param() => Series::RELATION_ANIME,
        ];

        $series = Series::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->createOne();

        $series->unsetRelations()->load([
            Series::RELATION_ANIME => function (BelongsToMany $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ]);

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new SeriesResource($series, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Index Endpoint shall support constrained eager loading of anime by year.
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
            IncludeParser::param() => Series::RELATION_ANIME,
        ];

        $series = Series::factory()
            ->has(
                Anime::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        [Anime::ATTRIBUTE_YEAR => 2000],
                        [Anime::ATTRIBUTE_YEAR => 2001],
                        [Anime::ATTRIBUTE_YEAR => 2002],
                    ))
            )
            ->createOne();

        $series->unsetRelations()->load([
            Series::RELATION_ANIME => function (BelongsToMany $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ]);

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new SeriesResource($series, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
