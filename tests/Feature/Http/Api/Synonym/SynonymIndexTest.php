<?php

namespace Tests\Feature\Http\Api\Synonym;

use App\Enums\AnimeSeason;
use App\Enums\Filter\TrashedStatus;
use App\Http\Resources\SynonymCollection;
use App\Http\Resources\SynonymResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Synonym;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class SynonymIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Synonym Index Endpoint shall return a collection of Synonym Resources with all allowed include paths.
     *
     * @return void
     */
    public function testDefault()
    {
        Synonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $synonyms = Synonym::with(SynonymCollection::allowedIncludePaths())->get();

        $response = $this->get(route('api.synonym.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonyms, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated()
    {
        Synonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.synonym.index'));

        $response->assertJsonStructure([
            SynonymCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Synonym Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(SynonymCollection::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Synonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $synonyms = Synonym::with($included_paths->all())->get();

        $response = $this->get(route('api.synonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonyms, QueryParser::make($parameters))
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
            'deleted_at',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                SynonymResource::$wrap => $included_fields->join(','),
            ],
        ];

        Synonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $synonyms = Synonym::with(SynonymCollection::allowedIncludePaths())->get();

        $response = $this->get(route('api.synonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonyms, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts()
    {
        $allowed_sorts = collect(SynonymCollection::allowedSortFields());
        $included_sorts = $allowed_sorts->random($this->faker->numberBetween(1, count($allowed_sorts)))->map(function ($included_sort) {
            if ($this->faker->boolean()) {
                return Str::of('-')
                    ->append($included_sort)
                    ->__toString();
            }

            return $included_sort;
        });

        $parameters = [
            QueryParser::PARAM_SORT => $included_sorts->join(','),
        ];

        $parser = QueryParser::make($parameters);

        Synonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $builder = Synonym::with(SynonymCollection::allowedIncludePaths());

        foreach ($parser->getSorts() as $field => $isAsc) {
            $builder = $builder->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
        }

        $response = $this->get(route('api.synonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($builder->get(), QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support filtering by created_at.
     *
     * @return void
     */
    public function testCreatedAtFilter()
    {
        $created_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'created_at' => $created_filter,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($created_filter), function () {
            Synonym::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Synonym::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        $synonym = Synonym::where('created_at', $created_filter)->get();

        $response = $this->get(route('api.synonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonym, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support filtering by updated_at.
     *
     * @return void
     */
    public function testUpdatedAtFilter()
    {
        $updated_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'updated_at' => $updated_filter,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($updated_filter), function () {
            Synonym::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Synonym::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        $synonym = Synonym::where('updated_at', $updated_filter)->get();

        $response = $this->get(route('api.synonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonym, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter()
    {
        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
        ];

        Synonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $delete_synonym = Synonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $delete_synonym->each(function ($synonym) {
            $synonym->delete();
        });

        $synonym = Synonym::withoutTrashed()->get();

        $response = $this->get(route('api.synonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonym, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter()
    {
        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITH,
            ],
        ];

        Synonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $delete_synonym = Synonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $delete_synonym->each(function ($synonym) {
            $synonym->delete();
        });

        $synonym = Synonym::withTrashed()->get();

        $response = $this->get(route('api.synonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonym, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter()
    {
        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::ONLY,
            ],
        ];

        Synonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $delete_synonym = Synonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $delete_synonym->each(function ($synonym) {
            $synonym->delete();
        });

        $synonym = Synonym::onlyTrashed()->get();

        $response = $this->get(route('api.synonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonym, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support filtering by deleted_at.
     *
     * @return void
     */
    public function testDeletedAtFilter()
    {
        $deleted_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'deleted_at' => $deleted_filter,
                'trashed' => TrashedStatus::WITH,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($deleted_filter), function () {
            Synonym::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Synonym::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        $synonym = Synonym::withTrashed()->where('deleted_at', $deleted_filter)->get();

        $response = $this->get(route('api.synonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonym, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support constrained eager loading of anime by season.
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
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $synonyms = Synonym::with([
            'anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.synonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonyms, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support constrained eager loading of anime by year.
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
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $synonyms = Synonym::with([
            'anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
            },
        ])
        ->get();

        $response = $this->get(route('api.synonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonyms, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
