<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\FeaturedTheme;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
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
use App\Http\Api\Schema\Admin\FeaturedThemeSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Admin\Collection\FeaturedThemeCollection;
use App\Http\Resources\Admin\Resource\FeaturedThemeResource;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class FeaturedThemeIndexTest.
 */
class FeaturedThemeIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Featured Theme Index Endpoint shall return a collection of Featured Theme Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $publicCount = $this->faker->randomDigitNotNull();

        $featuredThemes = FeaturedTheme::factory()->count($publicCount)->create();

        Collection::times($this->faker->randomDigitNotNull(), function() {
            FeaturedTheme::factory()->create([
                FeaturedTheme::ATTRIBUTE_START_AT => $this->faker->dateTimeBetween('+1 day', '+30 years'),
            ]);
        });

        Collection::times($this->faker->randomDigitNotNull(), function() {
            FeaturedTheme::factory()->create([
                FeaturedTheme::ATTRIBUTE_START_AT => null,
            ]);
        });

        $response = $this->get(route('api.featuredtheme.index'));

        $response->assertJsonCount($publicCount, FeaturedThemeCollection::$wrap);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeCollection($featuredThemes, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Featured Theme Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        FeaturedTheme::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.featuredtheme.index'));

        $response->assertJsonStructure([
            FeaturedThemeCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Featured Theme Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new FeaturedThemeSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        FeaturedTheme::factory()
            ->for(
                AnimeThemeEntry::factory()
                    ->for(
                        AnimeTheme::factory()
                            ->for(Anime::factory()->has(Image::factory()->count($this->faker->randomDigitNotNull())))
                            ->for(Song::factory()->has(Artist::factory()->count($this->faker->randomDigitNotNull())))
                    )
            )
            ->for(Video::factory())
            ->for(User::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $featuredThemes = FeaturedTheme::with($includedPaths->all())->get();

        $response = $this->get(route('api.featuredtheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeCollection($featuredThemes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Featured Theme Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new FeaturedThemeSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                FeaturedThemeResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $featuredThemes = FeaturedTheme::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.featuredtheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeCollection($featuredThemes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Featured Theme Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new FeaturedThemeSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Direction::getRandomInstance()),
        ];

        $query = new Query($parameters);

        FeaturedTheme::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.featuredtheme.index', $parameters));

        $featuredThemes = $this->sort(FeaturedTheme::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeCollection($featuredThemes, $query))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Featured Theme Index Endpoint shall support filtering by created_at.
     *
     * @return void
     */
    public function testCreatedAtFilter(): void
    {
        $createdFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_CREATED_AT => $createdFilter,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($createdFilter, function () {
            FeaturedTheme::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            FeaturedTheme::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $featuredTheme = FeaturedTheme::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.featuredtheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeCollection($featuredTheme, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Featured Theme Index Endpoint shall support filtering by updated_at.
     *
     * @return void
     */
    public function testUpdatedAtFilter(): void
    {
        $updatedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_UPDATED_AT => $updatedFilter,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($updatedFilter, function () {
            FeaturedTheme::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            FeaturedTheme::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $featuredTheme = FeaturedTheme::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.featuredtheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeCollection($featuredTheme, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Featured Theme Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        FeaturedTheme::factory()->count($this->faker->randomDigitNotNull())->create();

        FeaturedTheme::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $featuredTheme = FeaturedTheme::withoutTrashed()->get();

        $response = $this->get(route('api.featuredtheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeCollection($featuredTheme, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Featured Theme Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        FeaturedTheme::factory()->count($this->faker->randomDigitNotNull())->create();

        FeaturedTheme::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $featuredTheme = FeaturedTheme::withTrashed()->get();

        $response = $this->get(route('api.featuredtheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeCollection($featuredTheme, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Featured Theme Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        FeaturedTheme::factory()->count($this->faker->randomDigitNotNull())->create();

        FeaturedTheme::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $featuredTheme = FeaturedTheme::onlyTrashed()->get();

        $response = $this->get(route('api.featuredtheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeCollection($featuredTheme, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Featured Theme Index Endpoint shall support filtering by deleted_at.
     *
     * @return void
     */
    public function testDeletedAtFilter(): void
    {
        $deletedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_DELETED_AT => $deletedFilter,
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            FeaturedTheme::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            FeaturedTheme::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        $featuredTheme = FeaturedTheme::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.featuredtheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeCollection($featuredTheme, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
