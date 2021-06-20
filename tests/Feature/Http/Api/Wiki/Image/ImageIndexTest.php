<?php

declare(strict_types=1);

namespace Http\Api\Wiki\Image;

use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Http\Api\QueryParser;
use App\Http\Resources\Wiki\Collection\ImageCollection;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class ImageIndexTest.
 */
class ImageIndexTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

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
        $allowedPaths = collect(ImageCollection::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $images = Image::with($includedPaths->all())->get();

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

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                ImageResource::$wrap => $includedFields->join(','),
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
        $allowedSorts = collect(ImageCollection::allowedSortFields());
        $includedSorts = $allowedSorts->random($this->faker->numberBetween(1, count($allowedSorts)))->map(function (string $includedSort) {
            if ($this->faker->boolean()) {
                return Str::of('-')
                    ->append($includedSort)
                    ->__toString();
            }

            return $includedSort;
        });

        $parameters = [
            QueryParser::PARAM_SORT => $includedSorts->join(','),
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
        $createdFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'created_at' => $createdFilter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($createdFilter), function () {
            Image::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Image::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $image = Image::where('created_at', $createdFilter)->get();

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
        $updatedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'updated_at' => $updatedFilter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($updatedFilter), function () {
            Image::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Image::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $image = Image::where('updated_at', $updatedFilter)->get();

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
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Image::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteImage = Image::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteImage->each(function (Image $image) {
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
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Image::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteImage = Image::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteImage->each(function (Image $image) {
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
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Image::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteImage = Image::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteImage->each(function (Image $image) {
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
        $deletedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'deleted_at' => $deletedFilter,
                'trashed' => TrashedStatus::WITH,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($deletedFilter), function () {
            $images = Image::factory()->count($this->faker->randomDigitNotNull)->create();
            $images->each(function (Image $image) {
                $image->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            $images = Image::factory()->count($this->faker->randomDigitNotNull)->create();
            $images->each(function (Image $image) {
                $image->delete();
            });
        });

        $image = Image::withTrashed()->where('deleted_at', $deletedFilter)->get();

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
        $facetFilter = ImageFacet::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'facet' => $facetFilter->key,
            ],
        ];

        Image::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $images = Image::where('facet', $facetFilter->value)->get();

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
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $seasonFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $images = Image::with([
            'anime' => function (BelongsToMany $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
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
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $yearFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Image::factory()
            ->has(
                Anime::factory()
                ->count($this->faker->randomDigitNotNull)
                ->state([
                    'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                ])
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $images = Image::with([
            'anime' => function (BelongsToMany $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
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
