<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Dump;

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
use App\Http\Api\Schema\Admin\DumpSchema;
use App\Http\Resources\Admin\Collection\DumpCollection;
use App\Http\Resources\Admin\Resource\DumpResource;
use App\Models\Admin\Dump;
use App\Models\BaseModel;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Class DumpIndexTest.
 */
class DumpIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Dump Index Endpoint shall return a collection of Dump Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $dumps = Dump::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.dump.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new DumpCollection($dumps, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Dump Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        Dump::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.dump.index'));

        $response->assertJsonStructure([
            DumpCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Dump Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new DumpSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                DumpResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $dumps = Dump::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.dump.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new DumpCollection($dumps, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Dump Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new DumpSchema();

        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Direction::getRandomInstance()),
        ];

        $query = new Query($parameters);

        Dump::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.dump.index', $parameters));

        $dumps = $this->sort(Dump::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new DumpCollection($dumps, $query))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Dump Index Endpoint shall support filtering by created_at.
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
            Dump::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Dump::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $dump = Dump::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.dump.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new DumpCollection($dump, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Dump Index Endpoint shall support filtering by updated_at.
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
            Dump::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Dump::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $dump = Dump::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.dump.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new DumpCollection($dump, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Dump Index Endpoint shall support filtering by trashed.
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

        Dump::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteDump = Dump::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteDump->each(function (Dump $dump) {
            $dump->delete();
        });

        $dump = Dump::withoutTrashed()->get();

        $response = $this->get(route('api.dump.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new DumpCollection($dump, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Dump Index Endpoint shall support filtering by trashed.
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

        Dump::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteDump = Dump::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteDump->each(function (Dump $dump) {
            $dump->delete();
        });

        $dump = Dump::withTrashed()->get();

        $response = $this->get(route('api.dump.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new DumpCollection($dump, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Dump Index Endpoint shall support filtering by trashed.
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

        Dump::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteDump = Dump::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteDump->each(function (Dump $dump) {
            $dump->delete();
        });

        $dump = Dump::onlyTrashed()->get();

        $response = $this->get(route('api.dump.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new DumpCollection($dump, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Dump Index Endpoint shall support filtering by deleted_at.
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
            $dumps = Dump::factory()->count($this->faker->randomDigitNotNull())->create();
            $dumps->each(function (Dump $dump) {
                $dump->delete();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            $dumps = Dump::factory()->count($this->faker->randomDigitNotNull())->create();
            $dumps->each(function (Dump $dump) {
                $dump->delete();
            });
        });

        $dump = Dump::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.dump.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new DumpCollection($dump, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
