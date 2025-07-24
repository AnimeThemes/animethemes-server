<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\AnimeSeries;

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\AnimeSeriesSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeSeriesResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class AnimeSeriesShowTest extends TestCase
{
    use WithFaker;

    /**
     * The Anime Series Show Endpoint shall return an error if the anime series does not exist.
     */
    public function testNotFound(): void
    {
        $anime = Anime::factory()->createOne();
        $series = Series::factory()->createOne();

        $response = $this->get(route('api.animeseries.show', ['anime' => $anime, 'series' => $series]));

        $response->assertNotFound();
    }

    /**
     * By default, the Anime Series Show Endpoint shall return an Anime Series Resource.
     */
    public function testDefault(): void
    {
        $animeSeries = AnimeSeries::factory()
            ->for(Anime::factory())
            ->for(Series::factory())
            ->createOne();

        $response = $this->get(route('api.animeseries.show', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series]));

        $animeSeries->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeSeriesResource($animeSeries, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Series Show Endpoint shall allow inclusion of related resources.
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new AnimeSeriesSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $animeSeries = AnimeSeries::factory()
            ->for(Anime::factory())
            ->for(Series::factory())
            ->createOne();

        $response = $this->get(route('api.animeseries.show', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series] + $parameters));

        $animeSeries->unsetRelations()->load($includedPaths->all());

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeSeriesResource($animeSeries, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Series Show Endpoint shall implement sparse fieldsets.
     */
    public function testSparseFieldsets(): void
    {
        $schema = new AnimeSeriesSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AnimeSeriesResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $animeSeries = AnimeSeries::factory()
            ->for(Anime::factory())
            ->for(Series::factory())
            ->createOne();

        $response = $this->get(route('api.animeseries.show', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series] + $parameters));

        $animeSeries->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeSeriesResource($animeSeries, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Series Show Endpoint shall support constrained eager loading of anime by media_format.
     */
    public function testAnimeByMediaFormat(): void
    {
        $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
            ],
            IncludeParser::param() => AnimeSeries::RELATION_ANIME,
        ];

        $animeSeries = AnimeSeries::factory()
            ->for(Anime::factory())
            ->for(Series::factory())
            ->createOne();

        $response = $this->get(route('api.animeseries.show', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series] + $parameters));

        $animeSeries->unsetRelations()->load([
            AnimeSeries::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
                $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeSeriesResource($animeSeries, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Series Show Endpoint shall support constrained eager loading of anime by season.
     */
    public function testAnimeBySeason(): void
    {
        $seasonFilter = Arr::random(AnimeSeason::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
            ],
            IncludeParser::param() => AnimeSeries::RELATION_ANIME,
        ];

        $animeSeries = AnimeSeries::factory()
            ->for(Anime::factory())
            ->for(Series::factory())
            ->createOne();

        $response = $this->get(route('api.animeseries.show', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series] + $parameters));

        $animeSeries->unsetRelations()->load([
            AnimeSeries::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeSeriesResource($animeSeries, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Series Show Endpoint shall support constrained eager loading of anime by year.
     */
    public function testAnimeByYear(): void
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => AnimeSeries::RELATION_ANIME,
        ];

        $animeSeries = AnimeSeries::factory()
            ->for(
                Anime::factory()
                    ->state([
                        Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
            ->for(Series::factory())
            ->createOne();

        $response = $this->get(route('api.animeseries.show', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series] + $parameters));

        $animeSeries->unsetRelations()->load([
            AnimeSeries::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeSeriesResource($animeSeries, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
