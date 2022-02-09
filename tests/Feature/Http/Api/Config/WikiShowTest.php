<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Config;

use App\Constants\Config\WikiConstants;
use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Config\WikiQuery;
use App\Http\Api\Schema\Config\WikiSchema;
use App\Http\Resources\Config\Resource\WikiResource;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class WikiShowTest.
 */
class WikiShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Wiki Show Endpoint shall return a Flags Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $video = Video::factory()->createOne();

        Config::set(WikiConstants::FEATURED_THEME_SETTING_QUALIFIED, $video->basename);

        $response = $this->get(route('api.config.wiki.show'));

        $response->assertJson(
            json_decode(
                json_encode(
                    WikiResource::make(WikiQuery::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Wiki Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $video = Video::factory()->createOne();

        Config::set(WikiConstants::FEATURED_THEME_SETTING_QUALIFIED, $video->basename);

        $schema = new WikiSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                WikiResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $response = $this->get(route('api.config.wiki.show'));

        $response->assertJson(
            json_decode(
                json_encode(
                    WikiResource::make(WikiQuery::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
