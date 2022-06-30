<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\AnimeImage;

use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Pivot\AnimeImage\AnimeImageReadQuery;
use App\Http\Api\Schema\Pivot\AnimeImageSchema;
use App\Http\Resources\Pivot\Collection\AnimeImageCollection;
use App\Http\Resources\Pivot\Resource\AnimeImageResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\AnimeImage;
use App\Pivots\BasePivot;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class AnimeImageIndexTest.
 */
class AnimeImageIndexTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Anime Image Index Endpoint shall return a collection of Anime Image Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeImage::factory()
                ->for(Anime::factory())
                ->for(Image::factory())
                ->create();
        });

        $animeImages = AnimeImage::all();

        $response = $this->get(route('api.animeimage.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeImageCollection($animeImages, new AnimeImageReadQuery()))
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
            AnimeImage::factory()
                ->for(Anime::factory())
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.animeimage.index'));

        $response->assertJsonStructure([
            AnimeImageCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Anime Image Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new AnimeImageSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeImage::factory()
                ->for(Anime::factory())
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.animeimage.index', $parameters));

        $animeImages = AnimeImage::with($includedPaths->all())->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeImageCollection($animeImages, new AnimeImageReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Image Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new AnimeImageSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AnimeImageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeImage::factory()
                ->for(Anime::factory())
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.animeimage.index', $parameters));

        $animeImages = AnimeImage::all();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeImageCollection($animeImages, new AnimeImageReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Image Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new AnimeImageSchema();

        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Direction::getRandomInstance()),
        ];

        $query = new AnimeImageReadQuery($parameters);

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeImage::factory()
                ->for(Anime::factory())
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.animeimage.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    $query->collection($query->index())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Image Index Endpoint shall support filtering by created_at.
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
                AnimeImage::factory()
                    ->for(Anime::factory())
                    ->for(Image::factory())
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                AnimeImage::factory()
                    ->for(Anime::factory())
                    ->for(Image::factory())
                    ->create();
            });
        });

        $animeImages = AnimeImage::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.animeimage.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeImageCollection($animeImages, new AnimeImageReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Image Index Endpoint shall support filtering by updated_at.
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
                AnimeImage::factory()
                    ->for(Anime::factory())
                    ->for(Image::factory())
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                AnimeImage::factory()
                    ->for(Anime::factory())
                    ->for(Image::factory())
                    ->create();
            });
        });

        $animeImages = AnimeImage::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.animeimage.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeImageCollection($animeImages, new AnimeImageReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Image Index Endpoint shall support constrained eager loading of images by facet.
     *
     * @return void
     */
    public function testImagesByFacet(): void
    {
        $facetFilter = ImageFacet::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Image::ATTRIBUTE_FACET => $facetFilter->description,
            ],
            IncludeParser::param() => AnimeImage::RELATION_IMAGE,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeImage::factory()
                ->for(Anime::factory())
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.animeimage.index', $parameters));

        $animeImages = AnimeImage::with([
            AnimeImage::RELATION_IMAGE => function (BelongsTo $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
            },
        ])
        ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeImageCollection($animeImages, new AnimeImageReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Image Show Endpoint shall support constrained eager loading of anime by season.
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
            IncludeParser::param() => AnimeImage::RELATION_ANIME,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeImage::factory()
                ->for(Anime::factory())
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.animeimage.index', $parameters));

        $animeImages = AnimeImage::with([
            AnimeImage::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ])
        ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeImageCollection($animeImages, new AnimeImageReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Image Show Endpoint shall support constrained eager loading of anime by year.
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
            IncludeParser::param() => AnimeImage::RELATION_ANIME,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () use ($yearFilter, $excludedYear) {
            AnimeImage::factory()
                ->for(
                    Anime::factory()
                        ->state([
                            Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                        ])
                )
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.animeimage.index', $parameters));

        $animeImages = AnimeImage::with([
            AnimeImage::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ])
        ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeImageCollection($animeImages, new AnimeImageReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
