<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Announcement;

use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query;
use App\Http\Resources\Admin\Collection\AnnouncementCollection;
use App\Http\Resources\Admin\Resource\AnnouncementResource;
use App\Models\Admin\Announcement;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class AnnouncementIndexTest.
 */
class AnnouncementIndexTest extends TestCase
{
    use RefreshDatabase;
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
     * The Announcement Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(AnnouncementCollection::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
        ];

        Announcement::factory()->count($this->faker->randomDigitNotNull())->create();
        $announcements = Announcement::with($includedPaths->all())->get();

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
     * The Announcement Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'content',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            FieldParser::$param => [
                AnnouncementResource::$wrap => $includedFields->join(','),
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
        $allowedSorts = collect([
            'id',
            'content',
            'created_at',
            'updated_at',
            'deleted_at',
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

        Announcement::factory()->count($this->faker->randomDigitNotNull())->create();

        $builder = Announcement::query();

        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach (AnnouncementCollection::sorts(collect([$sortCriterion])) as $sort) {
                $builder = $sort->applySort($builder);
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
                'created_at' => $createdFilter,
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

        $announcement = Announcement::query()->where('created_at', $createdFilter)->get();

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
                'updated_at' => $updatedFilter,
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

        $announcement = Announcement::query()->where('updated_at', $updatedFilter)->get();

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
                'trashed' => TrashedStatus::WITHOUT,
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
                'trashed' => TrashedStatus::WITH,
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
                'trashed' => TrashedStatus::ONLY,
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
                'deleted_at' => $deletedFilter,
                'trashed' => TrashedStatus::WITH,
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

        $announcement = Announcement::withTrashed()->where('deleted_at', $deletedFilter)->get();

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
