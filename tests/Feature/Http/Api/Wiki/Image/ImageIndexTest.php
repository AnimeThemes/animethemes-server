<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Image;

use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query;
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
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.image.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($images, Query::make())
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
        Image::factory()->count($this->faker->randomDigitNotNull())->create();

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
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(1, count($allowedPaths)));

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
        ];

        Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $images = Image::with($includedPaths->all())->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($images, Query::make($parameters))
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
            'id',
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
            FieldParser::$param => [
                ImageResource::$wrap => $includedFields->join(','),
            ],
        ];

        $images = Image::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($images, Query::make($parameters))
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
        $allowedSorts = collect([
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'path',
            'size',
            'mimetype',
            'facet',
        ]);

        $sortCount = $this->faker->numberBetween(1, count($allowedSorts));

        $includedSorts = $allowedSorts->random($sortCount)->map(function (string $includedSort) {
            if ($this->faker->boolean()) {
                return Str::of('-')
                    ->append($includedSort)
                    ->__toString();
            }

            return $includedSort;
        });

        $parameters = [
            SortParser::$param => $includedSorts->join(','),
        ];

        $query = Query::make($parameters);

        Image::factory()->count($this->faker->randomDigitNotNull())->create();

        $builder = Image::query();

        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach (ImageCollection::sorts(collect([$sortCriterion])) as $sort) {
                $builder = $sort->applySort($builder);
            }
        }

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($builder->get(), Query::make($parameters))
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
            FilterParser::$param => [
                'created_at' => $createdFilter,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($createdFilter, function () {
            Image::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Image::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $image = Image::query()->where('created_at', $createdFilter)->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($image, Query::make($parameters))
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
            FilterParser::$param => [
                'updated_at' => $updatedFilter,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($updatedFilter, function () {
            Image::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Image::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $image = Image::query()->where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($image, Query::make($parameters))
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
            FilterParser::$param => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Image::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteImage = Image::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteImage->each(function (Image $image) {
            $image->delete();
        });

        $image = Image::withoutTrashed()->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($image, Query::make($parameters))
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
            FilterParser::$param => [
                'trashed' => TrashedStatus::WITH,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Image::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteImage = Image::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteImage->each(function (Image $image) {
            $image->delete();
        });

        $image = Image::withTrashed()->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($image, Query::make($parameters))
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
            FilterParser::$param => [
                'trashed' => TrashedStatus::ONLY,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Image::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteImage = Image::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteImage->each(function (Image $image) {
            $image->delete();
        });

        $image = Image::onlyTrashed()->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($image, Query::make($parameters))
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
            FilterParser::$param => [
                'deleted_at' => $deletedFilter,
                'trashed' => TrashedStatus::WITH,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            $images = Image::factory()->count($this->faker->randomDigitNotNull())->create();
            $images->each(function (Image $image) {
                $image->delete();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            $images = Image::factory()->count($this->faker->randomDigitNotNull())->create();
            $images->each(function (Image $image) {
                $image->delete();
            });
        });

        $image = Image::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($image, Query::make($parameters))
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
            FilterParser::$param => [
                'facet' => $facetFilter->description,
            ],
        ];

        Image::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $images = Image::query()->where('facet', $facetFilter->value)->get();

        $response = $this->get(route('api.image.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ImageCollection::make($images, Query::make($parameters))
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
            FilterParser::$param => [
                'season' => $seasonFilter->description,
            ],
            IncludeParser::$param => 'anime',
        ];

        Image::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
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
                    ImageCollection::make($images, Query::make($parameters))
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
            FilterParser::$param => [
                'year' => $yearFilter,
            ],
            IncludeParser::$param => 'anime',
        ];

        Image::factory()
            ->has(
                Anime::factory()
                ->count($this->faker->randomDigitNotNull())
                ->state([
                    'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                ])
            )
            ->count($this->faker->randomDigitNotNull())
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
                    ImageCollection::make($images, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
