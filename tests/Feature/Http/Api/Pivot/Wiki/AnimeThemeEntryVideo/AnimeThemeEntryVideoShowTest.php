<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\AnimeThemeEntryVideo;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\AnimeThemeEntryVideoSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeThemeEntryVideoResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class AnimeThemeEntryVideoShowTest.
 */
class AnimeThemeEntryVideoShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Anime Theme Entry Video Show Endpoint shall return an error if the anime video does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->create();

        $video = Video::factory()->createOne();

        $response = $this->get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entry, 'video' => $video]));

        $response->assertNotFound();
    }

    /**
     * By default, the Anime Theme Entry Video Show Endpoint shall return an Anime Theme Entry Video Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $response = $this->get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video]));

        $entryVideo->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeThemeEntryVideoResource($entryVideo, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new AnimeThemeEntryVideoSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $response = $this->get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

        $entryVideo->unsetRelations()->load($includedPaths->all());

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeThemeEntryVideoResource($entryVideo, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new AnimeThemeEntryVideoSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AnimeThemeEntryVideoResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $response = $this->get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

        $entryVideo->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeThemeEntryVideoResource($entryVideo, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of entries by nsfw.
     *
     * @return void
     */
    public function testEntryByNsfw(): void
    {
        $nsfwFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_NSFW => $nsfwFilter,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_ENTRY,
        ];

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $response = $this->get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

        $entryVideo->unsetRelations()->load([
            AnimeThemeEntryVideo::RELATION_ENTRY => function (BelongsTo $query) use ($nsfwFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeThemeEntryVideoResource($entryVideo, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of entries by spoiler.
     *
     * @return void
     */
    public function testEntryBySpoiler(): void
    {
        $spoilerFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_SPOILER => $spoilerFilter,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_ENTRY,
        ];

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $response = $this->get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

        $entryVideo->unsetRelations()->load([
            AnimeThemeEntryVideo::RELATION_ENTRY => function (BelongsTo $query) use ($spoilerFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeThemeEntryVideoResource($entryVideo, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of entries by version.
     *
     * @return void
     */
    public function testEntryByVersion(): void
    {
        $versionFilter = $this->faker->randomDigitNotNull();

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_ENTRY,
        ];

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $response = $this->get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

        $entryVideo->unsetRelations()->load([
            AnimeThemeEntryVideo::RELATION_ENTRY => function (BelongsTo $query) use ($versionFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeThemeEntryVideoResource($entryVideo, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of videos by lyrics.
     *
     * @return void
     */
    public function testVideoByLyrics(): void
    {
        $lyricsFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_LYRICS => $lyricsFilter,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
        ];

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $response = $this->get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

        $entryVideo->unsetRelations()->load([
            AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($lyricsFilter) {
                $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeThemeEntryVideoResource($entryVideo, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of videos by nc.
     *
     * @return void
     */
    public function testVideoByNc(): void
    {
        $ncFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_NC => $ncFilter,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
        ];

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $response = $this->get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

        $entryVideo->unsetRelations()->load([
            AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($ncFilter) {
                $query->where(Video::ATTRIBUTE_NC, $ncFilter);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeThemeEntryVideoResource($entryVideo, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of videos by overlap.
     *
     * @return void
     */
    public function testVideoByOverlap(): void
    {
        $overlapFilter = VideoOverlap::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_OVERLAP => $overlapFilter->description,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
        ];

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $response = $this->get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

        $entryVideo->unsetRelations()->load([
            AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($overlapFilter) {
                $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeThemeEntryVideoResource($entryVideo, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of videos by resolution.
     *
     * @return void
     */
    public function testVideoByResolution(): void
    {
        $resolutionFilter = $this->faker->randomNumber();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_RESOLUTION => $resolutionFilter,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
        ];

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $response = $this->get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

        $entryVideo->unsetRelations()->load([
            AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($resolutionFilter) {
                $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeThemeEntryVideoResource($entryVideo, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of videos by source.
     *
     * @return void
     */
    public function testVideoBySource(): void
    {
        $sourceFilter = VideoSource::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_SOURCE => $sourceFilter->description,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
        ];

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $response = $this->get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

        $entryVideo->unsetRelations()->load([
            AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($sourceFilter) {
                $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeThemeEntryVideoResource($entryVideo, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of videos by subbed.
     *
     * @return void
     */
    public function testVideoBySubbed(): void
    {
        $subbedFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_SUBBED => $subbedFilter,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
        ];

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $response = $this->get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

        $entryVideo->unsetRelations()->load([
            AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($subbedFilter) {
                $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeThemeEntryVideoResource($entryVideo, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of videos by uncen.
     *
     * @return void
     */
    public function testVideoByUncen(): void
    {
        $uncenFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_UNCEN => $uncenFilter,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
        ];

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $response = $this->get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

        $entryVideo->unsetRelations()->load([
            AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($uncenFilter) {
                $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeThemeEntryVideoResource($entryVideo, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
