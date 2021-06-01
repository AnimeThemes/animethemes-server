<?php declare(strict_types=1);

namespace Http\Api\Announcement;

use App\Http\Resources\AnnouncementResource;
use App\JsonApi\QueryParser;
use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class AnnouncementShowTest
 * @package Http\Api\Announcement
 */
class AnnouncementShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Annouc Show Endpoint shall return an Announcement Resource.
     *
     * @return void
     */
    public function testDefault()
    {
        $announcement = Announcement::factory()->create();

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
     * The Announcement Show Endpoint shall return an Announcement Resource for soft deleted images.
     *
     * @return void
     */
    public function testSoftDelete()
    {
        $announcement = Announcement::factory()->createOne();

        $announcement->delete();

        $announcement->unsetRelations();

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
        $allowedPaths = collect(AnnouncementResource::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Announcement::factory()->create();
        $announcement = Announcement::with($includedPaths->all())->first();

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
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                AnnouncementResource::$wrap => $includedFields->join(','),
            ],
        ];

        $announcement = Announcement::factory()->create();

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
