<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Audio;

use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\AudioSchema;
use App\Http\Resources\Wiki\Resource\AudioResource;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class AudioShowTest.
 */
class AudioShowTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Audio Show Endpoint shall return an Audio Resource.
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
                    new AudioResource($audio, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Show Endpoint shall return an Audio Resource for soft deleted audios.
     *
     * @return void
     */
    public function testSoftDelete(): void
    {
        $audio = Audio::factory()->trashed()->createOne();

        $response = $this->get(route('api.audio.show', ['audio' => $audio]));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AudioResource($audio, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new AudioSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $audio = Audio::factory()
            ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $response = $this->get(route('api.audio.show', ['audio' => $audio] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AudioResource($audio, new Query($parameters))
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
                    new AudioResource($audio, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
