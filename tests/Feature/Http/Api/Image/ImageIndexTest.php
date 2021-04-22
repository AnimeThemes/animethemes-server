<?php

namespace Tests\Feature\Http\Api\Image;

use App\Enums\AnimeSeason;
use App\Enums\Filter\TrashedStatus;
use App\Enums\ImageFacet;
use App\Http\Resources\ImageCollection;
use App\Http\Resources\ImageResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\Image;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Str;
use Tests\TestCase;

class ImageIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * By default, the Image Index Endpoint shall return a collection of Image Resources.
     *
     * @return void
     */
    public function testDefault()
    {
        $images = Image::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.image.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($images, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated()
    {
        Image::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.image.index'));

        $response->assertJsonStructure([
            ImageCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Image Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(ImageCollection::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $images = Image::with($included_paths->all())->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($images, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $fields = collect([
            'image_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'path',
            'size',
            'mimetype',
            'facet',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                ImageResource::$wrap => $included_fields->join(','),
            ],
        ];

        $images = Image::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($images, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts()
    {
        $allowed_sorts = collect(ImageCollection::allowedSortFields());
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

        Image::factory()->count($this->faker->randomDigitNotNull)->create();

        $builder = Image::query();

        foreach ($parser->getSorts() as $field => $isAsc) {
            $builder = $builder->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
        }

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($builder->get(), QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Index Endpoint shall support filtering by created_at.
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
            Image::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Image::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $image = Image::where('created_at', $created_filter)->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($image, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Index Endpoint shall support filtering by updated_at.
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
            Image::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Image::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $image = Image::where('updated_at', $updated_filter)->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($image, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Index Endpoint shall support filtering by trashed.
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

        Image::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_image = Image::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_image->each(function ($image) {
            $image->delete();
        });

        $image = Image::withoutTrashed()->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($image, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Index Endpoint shall support filtering by trashed.
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

        Image::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_image = Image::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_image->each(function ($image) {
            $image->delete();
        });

        $image = Image::withTrashed()->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($image, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Index Endpoint shall support filtering by trashed.
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

        Image::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_image = Image::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_image->each(function ($image) {
            $image->delete();
        });

        $image = Image::onlyTrashed()->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($image, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Index Endpoint shall support filtering by deleted_at.
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
            $image = Image::factory()->count($this->faker->randomDigitNotNull)->create();
            $image->each(function ($item) {
                $item->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            $image = Image::factory()->count($this->faker->randomDigitNotNull)->create();
            $image->each(function ($item) {
                $item->delete();
            });
        });

        $image = Image::withTrashed()->where('deleted_at', $deleted_filter)->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($image, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Index Endpoint shall support filtering by facet.
     *
     * @return void
     */
    public function testFacetFilter()
    {
        $facet_filter = ImageFacet::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'facet' => $facet_filter->key,
            ],
        ];

        Image::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $images = Image::where('facet', $facet_filter->value)->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($images, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Index Endpoint shall support constrained eager loading of anime by season.
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
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $images = Image::with([
            'anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($images, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Image Index Endpoint shall support constrained eager loading of anime by year.
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
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Image::factory()
            ->has(
                Anime::factory()
                ->count($this->faker->randomDigitNotNull)
                ->state([
                    'year' => $this->faker->boolean() ? $year_filter : $excluded_year,
                ])
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $images = Image::with([
            'anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
            },
        ])
        ->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($images, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
