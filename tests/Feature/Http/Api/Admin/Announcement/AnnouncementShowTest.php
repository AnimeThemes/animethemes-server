<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Announcement;

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Resources\Admin\Resource\AnnouncementResource;
use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnnouncementShowTest.
 */
class AnnouncementShowTest extends TestCase
{
    use WithFaker;

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
                    new AnnouncementResource($announcement, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Announcement Show Endpoint shall forbid users to access a private announcement.
     *
     * @return void
     */
    public function testUserCannotViewPrivate(): void
    {
        $announcement = Announcement::factory()->private()->create();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.announcement.show', ['announcement' => $announcement]));

        $response->assertForbidden();
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

        $response = $this->get(route('api.announcement.show', ['announcement' => $announcement] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnnouncementResource($announcement, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
