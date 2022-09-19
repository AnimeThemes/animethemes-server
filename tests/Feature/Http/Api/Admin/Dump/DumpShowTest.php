<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Dump;

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Admin\Dump\DumpReadQuery;
use App\Http\Api\Schema\Admin\DumpSchema;
use App\Http\Resources\Admin\Resource\DumpResource;
use App\Models\Admin\Dump;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class DumpShowTest.
 */
class DumpShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Dump Show Endpoint shall return a Dump Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $dump = Dump::factory()->create();

        $response = $this->get(route('api.dump.show', ['dump' => $dump]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new DumpResource($dump, new DumpReadQuery()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Dump Show Endpoint shall return a Dump Resource for soft deleted images.
     *
     * @return void
     */
    public function testSoftDelete(): void
    {
        $dump = Dump::factory()->createOne();

        $dump->delete();

        $dump->unsetRelations();

        $response = $this->get(route('api.dump.show', ['dump' => $dump]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new DumpResource($dump, new DumpReadQuery()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Dump Show Endpoint shall implement sparse fieldsets.
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

        $dump = Dump::factory()->create();

        $response = $this->get(route('api.dump.show', ['dump' => $dump]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new DumpResource($dump, new DumpReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
