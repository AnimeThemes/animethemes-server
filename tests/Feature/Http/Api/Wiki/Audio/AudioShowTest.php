<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Audio;

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Wiki\Audio\AudioReadQuery;
use App\Http\Api\Schema\Wiki\AudioSchema;
use App\Http\Resources\Wiki\Resource\AudioResource;
use App\Models\Wiki\Audio;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class AudioShowTest.
 */
class AudioShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Audio Show Endpoint shall return a Audio Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $audio = Audio::factory()->create();

        $response = $this->get(route('api.audio.show', ['audio' => $audio]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AudioResource($audio, new AudioReadQuery()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Show Endpoint shall return a Audio Resource for soft deleted audios.
     *
     * @return void
     */
    public function testSoftDelete(): void
    {
        $audio = Audio::factory()->createOne();

        $audio->delete();

        $response = $this->get(route('api.audio.show', ['audio' => $audio]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AudioResource($audio, new AudioReadQuery()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new AudioSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AudioResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $audio = Audio::factory()->create();

        $response = $this->get(route('api.audio.show', ['audio' => $audio] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AudioResource($audio, new AudioReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
