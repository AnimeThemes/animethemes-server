<?php

namespace Tests\Feature\Http\Api\Announcement;

use App\Http\Resources\AnnouncementResource;
use App\JsonApi\QueryParser;
use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AnnouncementShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Annouc Show Endpoint shall return an Announcement Resource with all allowed include paths.
     *
     * @return void
     */
    public function testDefault()
    {
        Announcement::factory()->create();
        $announcement = Announcement::with(AnnouncementResource::allowedIncludePaths())->first();

        $response = $this->get(route('api.announcement.show', ['announcement' => $announcement]));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementResource::make($announcement, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Announcement Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(AnnouncementResource::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Announcement::factory()->create();
        $announcement = Announcement::with($included_paths->all())->first();

        $response = $this->get(route('api.announcement.show', ['announcement' => $announcement]));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementResource::make($announcement, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Announcement Show Endpoint shall implement sparse fieldsets.
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

        Announcement::factory()->create();
        $announcement = Announcement::with(AnnouncementResource::allowedIncludePaths())->first();

        $response = $this->get(route('api.announcement.show', ['announcement' => $announcement]));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementResource::make($announcement, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
