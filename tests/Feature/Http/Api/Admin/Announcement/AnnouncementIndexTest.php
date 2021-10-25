<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Announcement;

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
use App\Http\Api\Query;
use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Resources\Admin\Collection\AnnouncementCollection;
use App\Http\Resources\Admin\Resource\AnnouncementResource;
use App\Models\Admin\Announcement;
use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class AnnouncementIndexTest.
 */
class AnnouncementIndexTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Announcement Index Endpoint shall return a collection of Announcement Resources.
     *
     * @return void
     */
    public function testDefault()
    {
        $announcements = Announcement::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.announcement.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcements, Query::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Announcement Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated()
    {
        Announcement::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.announcement.index'));

        $response->assertJsonStructure([
            AnnouncementCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Announcement Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $schema = new AnnouncementSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::$param => [
                AnnouncementResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $announcements = Announcement::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcements, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Announcement Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts()
    {
        $schema = new AnnouncementSchema();

        $fields = $schema->fields();

        $field = $fields[array_rand($fields)];

        $parameters = [
            SortParser::$param => $field->getSort()->format(Direction::getRandomInstance()),
        ];

        $query = Query::make($parameters);

        Announcement::factory()->count($this->faker->randomDigitNotNull())->create();

        $builder = Announcement::query();

        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach ($schema->sorts() as $sort) {
                $builder = $sort->applySort($sortCriterion, $builder);
            }
        }

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($builder->get(), Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Announcement Index Endpoint shall support filtering by created_at.
     *
     * @return void
     */
    public function testCreatedAtFilter()
    {
        $createdFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::$param => [
                BaseModel::ATTRIBUTE_CREATED_AT => $createdFilter,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($createdFilter, function () {
            Announcement::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Announcement::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $announcement = Announcement::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcement, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Announcement Index Endpoint shall support filtering by updated_at.
     *
     * @return void
     */
    public function testUpdatedAtFilter()
    {
        $updatedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::$param => [
                BaseModel::ATTRIBUTE_UPDATED_AT => $updatedFilter,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($updatedFilter, function () {
            Announcement::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Announcement::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $announcement = Announcement::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcement, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Announcement Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter()
    {
        $parameters = [
            FilterParser::$param => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Announcement::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteAnnouncement = Announcement::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteAnnouncement->each(function (Announcement $announcement) {
            $announcement->delete();
        });

        $announcement = Announcement::withoutTrashed()->get();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcement, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Announcement Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter()
    {
        $parameters = [
            FilterParser::$param => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Announcement::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteAnnouncement = Announcement::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteAnnouncement->each(function (Announcement $announcement) {
            $announcement->delete();
        });

        $announcement = Announcement::withTrashed()->get();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcement, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Announcement Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter()
    {
        $parameters = [
            FilterParser::$param => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Announcement::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteAnnouncement = Announcement::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteAnnouncement->each(function (Announcement $announcement) {
            $announcement->delete();
        });

        $announcement = Announcement::onlyTrashed()->get();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcement, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Announcement Index Endpoint shall support filtering by deleted_at.
     *
     * @return void
     */
    public function testDeletedAtFilter()
    {
        $deletedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::$param => [
                BaseModel::ATTRIBUTE_DELETED_AT => $deletedFilter,
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            $announcements = Announcement::factory()->count($this->faker->randomDigitNotNull())->create();
            $announcements->each(function (Announcement $announcement) {
                $announcement->delete();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            $announcements = Announcement::factory()->count($this->faker->randomDigitNotNull())->create();
            $announcements->each(function (Announcement $announcement) {
                $announcement->delete();
            });
        });

        $announcement = Announcement::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcement, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
