<?php

namespace Tests\Feature\Http\Api\Announcement;

use App\Http\Resources\AnnouncementCollection;
use App\Http\Resources\AnnouncementResource;
use App\JsonApi\QueryParser;
use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class AnnouncementIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Announcement Index Endpoint shall return a collection of Announcement Resources with all allowed include paths.
     *
     * @return void
     */
    public function testDefault()
    {
        Announcement::factory()->count($this->faker->randomDigitNotNull)->create();
        $announcements = Announcement::with(AnnouncementCollection::allowedIncludePaths())->get();

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
        $allowed_paths = collect(AnnouncementCollection::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Announcement::factory()->count($this->faker->randomDigitNotNull)->create();
        $announcements = Announcement::with($included_paths->all())->get();

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
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                AnnouncementResource::$resourceType => $included_fields->join(','),
            ],
        ];

        Announcement::factory()->count($this->faker->randomDigitNotNull)->create();
        $announcements = Announcement::with(AnnouncementCollection::allowedIncludePaths())->get();

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
        $allowed_sorts = collect(AnnouncementCollection::allowedSortFields());
        $included_sorts = $allowed_sorts->random($this->faker->numberBetween(1, count($allowed_sorts)))->map(function ($included_sort) {
            if ($this->faker->boolean()) {
                return Str::of('-')
                    ->append($included_sort)
                    ->__toString();
            }

            return $included_sort;
        });

        $parameters = [
            QueryParser::PARAM_SORT => $included_sorts->join(','),
        ];

        $parser = QueryParser::make($parameters);

        Announcement::factory()->count($this->faker->randomDigitNotNull)->create();

        $builder = Announcement::with(AnnouncementCollection::allowedIncludePaths());

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
}
