<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video\Script;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\Video\ScriptSchema;
use App\Http\Resources\Wiki\Video\Resource\ScriptResource;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class ScriptShowTest.
 */
class ScriptShowTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Script Show Endpoint shall return a Script Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $script = VideoScript::factory()->create();

        $response = $this->get(route('api.videoscript.show', ['videoscript' => $script]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ScriptResource($script, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Script Show Endpoint shall return a Script Resource for soft deleted scripts.
     *
     * @return void
     */
    public function testSoftDelete(): void
    {
        $script = VideoScript::factory()->createOne();

        $script->delete();

        $response = $this->get(route('api.videoscript.show', ['videoscript' => $script]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ScriptResource($script, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Script Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new ScriptSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $script = VideoScript::factory()
            ->for(Video::factory())
            ->createOne();

        $response = $this->get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ScriptResource($script, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Script Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new ScriptSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                ScriptResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $script = VideoScript::factory()->create();

        $response = $this->get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ScriptResource($script, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Script Show Endpoint shall support constrained eager loading of videos by lyrics.
     *
     * @return void
     */
    public function testVideosByLyrics(): void
    {
        $lyricsFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_LYRICS => $lyricsFilter,
            ],
            IncludeParser::param() => VideoScript::RELATION_VIDEO,
        ];

        $script = VideoScript::factory()
            ->for(Video::factory())
            ->create();

        $script->unsetRelations()->load([
            VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($lyricsFilter) {
                $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
            },
        ]);

        $response = $this->get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ScriptResource($script, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Script Show Endpoint shall support constrained eager loading of videos by nc.
     *
     * @return void
     */
    public function testVideosByNc(): void
    {
        $ncFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_NC => $ncFilter,
            ],
            IncludeParser::param() => VideoScript::RELATION_VIDEO,
        ];

        $script = VideoScript::factory()
            ->for(Video::factory())
            ->create();

        $script->unsetRelations()->load([
            VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($ncFilter) {
                $query->where(Video::ATTRIBUTE_NC, $ncFilter);
            },
        ]);

        $response = $this->get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ScriptResource($script, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Script Show Endpoint shall support constrained eager loading of videos by overlap.
     *
     * @return void
     */
    public function testVideosByOverlap(): void
    {
        $overlapFilter = VideoOverlap::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_OVERLAP => $overlapFilter->description,
            ],
            IncludeParser::param() => VideoScript::RELATION_VIDEO,
        ];

        $script = VideoScript::factory()
            ->for(Video::factory())
            ->create();

        $script->unsetRelations()->load([
            VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($overlapFilter) {
                $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
            },
        ]);

        $response = $this->get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ScriptResource($script, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Script Show Endpoint shall support constrained eager loading of videos by resolution.
     *
     * @return void
     */
    public function testVideosByResolution(): void
    {
        $resolutionFilter = $this->faker->randomNumber();
        $excludedResolution = $resolutionFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_RESOLUTION => $resolutionFilter,
            ],
            IncludeParser::param() => VideoScript::RELATION_VIDEO,
        ];

        $script = VideoScript::factory()
            ->for(
                Video::factory()->state([
                    Video::ATTRIBUTE_RESOLUTION => $this->faker->boolean() ? $resolutionFilter : $excludedResolution,
                ])
            )
            ->create();

        $script->unsetRelations()->load([
            VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($resolutionFilter) {
                $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
            },
        ]);

        $response = $this->get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ScriptResource($script, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Script Show Endpoint shall support constrained eager loading of videos by source.
     *
     * @return void
     */
    public function testVideosBySource(): void
    {
        $sourceFilter = VideoSource::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_SOURCE => $sourceFilter->description,
            ],
            IncludeParser::param() => VideoScript::RELATION_VIDEO,
        ];

        $script = VideoScript::factory()
            ->for(Video::factory())
            ->create();

        $script->unsetRelations()->load([
            VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($sourceFilter) {
                $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
            },
        ]);

        $response = $this->get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ScriptResource($script, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Script Show Endpoint shall support constrained eager loading of videos by subbed.
     *
     * @return void
     */
    public function testVideosBySubbed(): void
    {
        $subbedFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_SUBBED => $subbedFilter,
            ],
            IncludeParser::param() => VideoScript::RELATION_VIDEO,
        ];

        $script = VideoScript::factory()
            ->for(Video::factory())
            ->create();

        $script->unsetRelations()->load([
            VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($subbedFilter) {
                $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
            },
        ]);

        $response = $this->get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ScriptResource($script, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Script Show Endpoint shall support constrained eager loading of videos by uncen.
     *
     * @return void
     */
    public function testVideosByUncen(): void
    {
        $uncenFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_UNCEN => $uncenFilter,
            ],
            IncludeParser::param() => VideoScript::RELATION_VIDEO,
        ];

        $script = VideoScript::factory()
            ->for(Video::factory())
            ->create();

        $script->unsetRelations()->load([
            VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($uncenFilter) {
                $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
            },
        ]);

        $response = $this->get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ScriptResource($script, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
