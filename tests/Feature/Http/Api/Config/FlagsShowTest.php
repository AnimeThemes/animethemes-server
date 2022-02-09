<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Config;

use App\Constants\Config\FlagConstants;
use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Config\FlagsQuery;
use App\Http\Api\Schema\Config\FlagsSchema;
use App\Http\Resources\Config\Resource\FlagsResource;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class FlagsShowTest.
 */
class FlagsShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Flags Show Endpoint shall return a Flags Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Config::set(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, $this->faker->boolean());
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, $this->faker->boolean());
        Config::set(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED, $this->faker->boolean());

        $response = $this->get(route('api.config.flags.show'));

        $response->assertJson(
            json_decode(
                json_encode(
                    FlagsResource::make(FlagsQuery::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Flags Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        Config::set(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, $this->faker->boolean());
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, $this->faker->boolean());
        Config::set(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED, $this->faker->boolean());

        $schema = new FlagsSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                FlagsResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $response = $this->get(route('api.config.flags.show'));

        $response->assertJson(
            json_decode(
                json_encode(
                    FlagsResource::make(FlagsQuery::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
