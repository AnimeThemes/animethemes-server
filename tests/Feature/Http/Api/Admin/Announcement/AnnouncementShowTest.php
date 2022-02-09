<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Announcement;

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Admin\AnnouncementQuery;
use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Resources\Admin\Resource\AnnouncementResource;
use App\Models\Admin\Announcement;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class AnnouncementShowTest.
 */
class AnnouncementShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Announcement Show Endpoint shall return an Announcement Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $announcement = Announcement::factory()->create();

        $response = $this->get(route('api.announcement.show', ['announcement' => $announcement]));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementResource::make($announcement, AnnouncementQuery::make())
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
    public function testSoftDelete(): void
    {
        $announcement = Announcement::factory()->createOne();

        $announcement->delete();

        $announcement->unsetRelations();

        $response = $this->get(route('api.announcement.show', ['announcement' => $announcement]));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementResource::make($announcement, AnnouncementQuery::make())
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
    public function testSparseFieldsets(): void
    {
        $schema = new AnnouncementSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AnnouncementResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $announcement = Announcement::factory()->create();

        $response = $this->get(route('api.announcement.show', ['announcement' => $announcement]));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnnouncementResource::make($announcement, AnnouncementQuery::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
