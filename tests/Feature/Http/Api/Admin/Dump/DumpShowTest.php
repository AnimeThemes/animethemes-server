<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Dump;

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\DumpSchema;
use App\Http\Resources\Admin\Resource\DumpResource;
use App\Models\Admin\Dump;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class DumpShowTest.
 */
class DumpShowTest extends TestCase
{
    use WithFaker;

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
                    new DumpResource($dump, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Dump Show Endpoint shall forbid access to an unsafe dump.
     *
     * @return void
     */
    public function testCannotViewUnsafe(): void
    {
        $dump = Dump::factory()->unsafe()->create();

        $response = $this->get(route('api.dump.show', ['dump' => $dump]));

        $response->assertForbidden();
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

        $response = $this->get(route('api.dump.show', ['dump' => $dump] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new DumpResource($dump, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
