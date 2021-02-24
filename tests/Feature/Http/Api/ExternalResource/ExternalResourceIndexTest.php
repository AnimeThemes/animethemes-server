<?php

namespace Tests\Feature\Http\Api\ExternalResource;

use App\Enums\AnimeSeason;
use App\Enums\ResourceSite;
use App\Http\Resources\ExternalResourceCollection;
use App\Http\Resources\ExternalResourceResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\ExternalResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExternalResourceIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Resource Index Endpoint shall return a collection of ExternalResource Resources with all allowed include paths.
     *
     * @return void
     */
    public function testDefault()
    {
        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $resources = ExternalResource::with(ExternalResourceCollection::allowedIncludePaths())->get();

        $response = $this->get(route('api.resource.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resources, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated()
    {
        ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.resource.index'));

        $response->assertJsonStructure([
            ExternalResourceCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Resource Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(ExternalResourceCollection::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $resources = ExternalResource::with($included_paths->all())->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resources, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'link',
            'external_id',
            'site',
            'as',
            'created_at',
            'updated_at',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                ExternalResourceResource::$resourceType => $included_fields->join(','),
            ],
        ];

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $resources = ExternalResource::with(ExternalResourceCollection::allowedIncludePaths())->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resources, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts()
    {
        $allowed_sorts = collect(ExternalResourceCollection::allowedSortFields());
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

        ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();

        $builder = ExternalResource::with(ExternalResourceCollection::allowedIncludePaths());

        foreach ($parser->getSorts() as $field => $isAsc) {
            $builder = $builder->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
        }

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($builder->get(), QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support filtering by site.
     *
     * @return void
     */
    public function testSiteFilter()
    {
        $site_filter = ResourceSite::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'site' => $site_filter->key,
            ],
        ];

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $resources = ExternalResource::where('site', $site_filter->value)->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resources, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support constrained eager loading of anime by season.
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

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $resources = ExternalResource::with([
            'anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resources, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support constrained eager loading of anime by year.
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

        ExternalResource::factory()
            ->has(
                Anime::factory()
                ->count($this->faker->randomDigitNotNull)
                ->state([
                    'year' => $this->faker->boolean() ? $year_filter : $excluded_year,
                ])
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $resources = ExternalResource::with([
            'anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
            },
        ])
        ->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resources, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
