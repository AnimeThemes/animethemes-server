<?php declare(strict_types=1);

namespace Http\Api\Announcement;

use App\Enums\Filter\TrashedStatus;
use App\Http\Resources\AnnouncementCollection;
use App\Http\Resources\AnnouncementResource;
use App\JsonApi\QueryParser;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class AnnouncementIndexTest
 * @package Http\Api\Announcement
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
        $announcements = Announcement::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.announcement.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcements, QueryParser::make())
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
        Announcement::factory()->count($this->faker->randomDigitNotNull)->create();

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
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Announcement::factory()->count($this->faker->randomDigitNotNull)->create();
        $announcements = Announcement::with($includedPaths->all())->get();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcements, QueryParser::make($parameters))
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
            QueryParser::PARAM_FIELDS => [
                AnnouncementResource::$wrap => $includedFields->join(','),
            ],
        ];

        $announcements = Announcement::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcements, QueryParser::make($parameters))
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
        $allowedSorts = collect(AnnouncementCollection::allowedSortFields());
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

        Announcement::factory()->count($this->faker->randomDigitNotNull)->create();

        $builder = Announcement::query();

        foreach ($parser->getSorts() as $field => $isAsc) {
            $builder = $builder->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
        }

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($builder->get(), QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'created_at' => $createdFilter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($createdFilter), function () {
            Announcement::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Announcement::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $announcement = Announcement::where('created_at', $createdFilter)->get();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcement, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'updated_at' => $updatedFilter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($updatedFilter), function () {
            Announcement::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Announcement::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $announcement = Announcement::where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcement, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Announcement::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteAnnouncement = Announcement::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteAnnouncement->each(function (Announcement $announcement) {
            $announcement->delete();
        });

        $announcement = Announcement::withoutTrashed()->get();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcement, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITH,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Announcement::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteAnnouncement = Announcement::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteAnnouncement->each(function (Announcement $announcement) {
            $announcement->delete();
        });

        $announcement = Announcement::withTrashed()->get();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcement, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::ONLY,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Announcement::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteAnnouncement = Announcement::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteAnnouncement->each(function (Announcement $announcement) {
            $announcement->delete();
        });

        $announcement = Announcement::onlyTrashed()->get();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcement, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'deleted_at' => $deletedFilter,
                'trashed' => TrashedStatus::WITH,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($deletedFilter), function () {
            $announcements = Announcement::factory()->count($this->faker->randomDigitNotNull)->create();
            $announcements->each(function (Announcement $announcement) {
                $announcement->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            $announcements = Announcement::factory()->count($this->faker->randomDigitNotNull)->create();
            $announcements->each(function (Announcement $announcement) {
                $announcement->delete();
            });
        });

        $announcement = Announcement::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementCollection::make($announcement, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
