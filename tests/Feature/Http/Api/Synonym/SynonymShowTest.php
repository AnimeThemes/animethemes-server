<?php

namespace Tests\Feature\Http\Api\Synonym;

use App\Enums\AnimeSeason;
use App\Http\Resources\SynonymResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Synonym;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SynonymShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Synonym Show Endpoint shall return a Synonym Resource with all allowed include paths.
     *
     * @return void
     */
    public function testDefault()
    {
        Synonym::factory()
            ->for(Anime::factory())
            ->create();

        $synonym = Synonym::with(SynonymResource::allowedIncludePaths())->first();

        $response = $this->get(route('api.synonym.show', ['synonym' => $synonym]));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymResource::make($synonym, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(SynonymResource::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Synonym::factory()
            ->for(Anime::factory())
            ->create();

        $synonym = Synonym::with($included_paths->all())->first();

        $response = $this->get(route('api.synonym.show', ['synonym' => $synonym] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymResource::make($synonym, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'text',
            'created_at',
            'updated_at',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                SynonymResource::$resourceType => $included_fields->join(','),
            ],
        ];

        Synonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $synonym = Synonym::with(SynonymResource::allowedIncludePaths())->first();

        $response = $this->get(route('api.synonym.show', ['synonym' => $synonym] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymResource::make($synonym, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Show Endpoint shall support constrained eager loading of anime by season.
     *
     * @return void
     */
    public function testAnimeBySeason()
    {
        $season_filter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $season_filter->key,
            ],
        ];

        Synonym::factory()
            ->for(Anime::factory())
            ->create();

        $synonym = Synonym::with([
            'anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.synonym.show', ['synonym' => $synonym] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymResource::make($synonym, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Show Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function testAnimeByYear()
    {
        $year_filter = intval($this->faker->year());
        $excluded_year = $year_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $year_filter,
            ],
        ];

        Synonym::factory()
            ->for(
                Anime::factory()
                    ->state([
                        'year' => $this->faker->boolean() ? $year_filter : $excluded_year,
                    ])
            )
            ->create();

        $synonym = Synonym::with([
            'anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.synonym.show', ['synonym' => $synonym] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymResource::make($synonym, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
