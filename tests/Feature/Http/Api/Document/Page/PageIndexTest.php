<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Document\Page;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Document\PageSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Document\Collection\PageCollection;
use App\Http\Resources\Document\Resource\PageResource;
use App\Models\BaseModel;
use App\Models\Document\Page;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Class PageIndexTest.
 */
class PageIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Page Index Endpoint shall return a collection of Page Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $pages = Page::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.page.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new PageCollection($pages, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Page Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        Page::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.page.index'));

        $response->assertJsonStructure([
            PageCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Page Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new PageSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                PageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $pages = Page::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.page.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new PageCollection($pages, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Page Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new PageSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        Page::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.page.index', $parameters));

        $pages = $this->sort(Page::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new PageCollection($pages, $query)
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Page Index Endpoint shall support filtering by created_at.
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
            Page::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Page::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $page = Page::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.page.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new PageCollection($page, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Page Index Endpoint shall support filtering by updated_at.
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
            Page::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Page::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $page = Page::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.page.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new PageCollection($page, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Page Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Page::factory()->count($this->faker->randomDigitNotNull())->create();

        Page::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $page = Page::withoutTrashed()->get();

        $response = $this->get(route('api.page.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new PageCollection($page, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Page Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Page::factory()->count($this->faker->randomDigitNotNull())->create();

        Page::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $page = Page::withTrashed()->get();

        $response = $this->get(route('api.page.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new PageCollection($page, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Page Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Page::factory()->count($this->faker->randomDigitNotNull())->create();

        Page::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $page = Page::onlyTrashed()->get();

        $response = $this->get(route('api.page.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new PageCollection($page, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Page Index Endpoint shall support filtering by deleted_at.
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
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            Page::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Page::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        $page = Page::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.page.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new PageCollection($page, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
