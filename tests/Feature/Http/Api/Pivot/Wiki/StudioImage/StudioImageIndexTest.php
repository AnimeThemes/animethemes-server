<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\StudioImage;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
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
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\StudioImageSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Pivot\Wiki\Collection\StudioImageCollection;
use App\Http\Resources\Pivot\Wiki\Resource\StudioImageResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\StudioImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class StudioImageIndexTest.
 */
class StudioImageIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Studio Image Index Endpoint shall return a collection of Studio Image Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            StudioImage::factory()
                ->for(Studio::factory())
                ->for(Image::factory())
                ->create();
        });

        $studioImages = StudioImage::all();

        $response = $this->get(route('api.studioimage.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new StudioImageCollection($studioImages, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Image Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            StudioImage::factory()
                ->for(Studio::factory())
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.studioimage.index'));

        $response->assertJsonStructure([
            StudioImageCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Studio Image Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new StudioImageSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            StudioImage::factory()
                ->for(Studio::factory())
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.studioimage.index', $parameters));

        $studioImages = StudioImage::with($includedPaths->all())->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new StudioImageCollection($studioImages, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Image Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new StudioImageSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                StudioImageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            StudioImage::factory()
                ->for(Studio::factory())
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.studioimage.index', $parameters));

        $studioImages = StudioImage::all();

        $response->assertJson(
            json_decode(
                json_encode(
                    new StudioImageCollection($studioImages, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Image Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new StudioImageSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        Collection::times($this->faker->randomDigitNotNull(), function () {
            StudioImage::factory()
                ->for(Studio::factory())
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.studioimage.index', $parameters));

        $studioImages = $this->sort(StudioImage::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new StudioImageCollection($studioImages, $query)
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Image Index Endpoint shall support filtering by created_at.
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
                StudioImage::factory()
                    ->for(Studio::factory())
                    ->for(Image::factory())
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                StudioImage::factory()
                    ->for(Studio::factory())
                    ->for(Image::factory())
                    ->create();
            });
        });

        $studioImages = StudioImage::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.studioimage.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new StudioImageCollection($studioImages, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Image Index Endpoint shall support filtering by updated_at.
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
                StudioImage::factory()
                    ->for(Studio::factory())
                    ->for(Image::factory())
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                StudioImage::factory()
                    ->for(Studio::factory())
                    ->for(Image::factory())
                    ->create();
            });
        });

        $studioImages = StudioImage::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.studioimage.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new StudioImageCollection($studioImages, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Studio Image Index Endpoint shall support constrained eager loading of images by facet.
     *
     * @return void
     */
    public function testImagesByFacet(): void
    {
        $facetFilter = Arr::random(ImageFacet::cases());

        $parameters = [
            FilterParser::param() => [
                Image::ATTRIBUTE_FACET => $facetFilter->localize(),
            ],
            IncludeParser::param() => StudioImage::RELATION_IMAGE,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            StudioImage::factory()
                ->for(Studio::factory())
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.studioimage.index', $parameters));

        $studioImages = StudioImage::with([
            StudioImage::RELATION_IMAGE => function (BelongsTo $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new StudioImageCollection($studioImages, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
