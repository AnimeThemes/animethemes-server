<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Wiki\Video\VideoReadQuery;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class VideoShowTest.
 */
class VideoShowTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Video Show Endpoint shall return a Video Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $this->withoutEvents();

        $video = Video::factory()->create();

        $response = $this->get(route('api.video.show', ['video' => $video]));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, new VideoReadQuery())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Show Endpoint shall return a Video Resource for soft deleted videos.
     *
     * @return void
     */
    public function testSoftDelete(): void
    {
        $this->withoutEvents();

        $video = Video::factory()->createOne();

        $video->delete();

        $response = $this->get(route('api.video.show', ['video' => $video]));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, new VideoReadQuery())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new VideoSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Video::factory()
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(AnimeTheme::factory()->for(Anime::factory()))
            )
            ->create();

        $video = Video::with($includedPaths->all())->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, new VideoReadQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $this->withoutEvents();

        $schema = new VideoSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                VideoResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $video = Video::factory()->create();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, new VideoReadQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Show Endpoint shall support constrained eager loading of entries by nsfw.
     *
     * @return void
     */
    public function testEntriesByNsfw(): void
    {
        $nsfwFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_NSFW => $nsfwFilter,
            ],
            IncludeParser::param() => Video::RELATION_ANIMETHEMEENTRIES,
        ];

        Video::factory()
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(AnimeTheme::factory()->for(Anime::factory()))
            )
            ->create();

        $video = Video::with([
            Video::RELATION_ANIMETHEMEENTRIES => function (BelongsToMany $query) use ($nsfwFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, new VideoReadQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Show Endpoint shall support constrained eager loading of entries by spoiler.
     *
     * @return void
     */
    public function testEntriesBySpoiler(): void
    {
        $spoilerFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_SPOILER => $spoilerFilter,
            ],
            IncludeParser::param() => Video::RELATION_ANIMETHEMEENTRIES,
        ];

        Video::factory()
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(AnimeTheme::factory()->for(Anime::factory()))
            )
            ->create();

        $video = Video::with([
            Video::RELATION_ANIMETHEMEENTRIES => function (BelongsToMany $query) use ($spoilerFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, new VideoReadQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Show Endpoint shall support constrained eager loading of entries by version.
     *
     * @return void
     */
    public function testEntriesByVersion(): void
    {
        $versionFilter = $this->faker->randomDigitNotNull();
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
            ],
            IncludeParser::param() => Video::RELATION_ANIMETHEMEENTRIES,
        ];

        Video::factory()
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(AnimeTheme::factory()->for(Anime::factory()))
                    ->state(new Sequence(
                        [AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter],
                        [AnimeThemeEntry::ATTRIBUTE_VERSION => $excludedVersion],
                    ))
            )
            ->create();

        $video = Video::with([
            Video::RELATION_ANIMETHEMEENTRIES => function (BelongsToMany $query) use ($versionFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, new VideoReadQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Show Endpoint shall support constrained eager loading of themes by group.
     *
     * @return void
     */
    public function testThemesByGroup(): void
    {
        $groupFilter = $this->faker->word();
        $excludedGroup = $this->faker->word();

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_GROUP => $groupFilter,
            ],
            IncludeParser::param() => Video::RELATION_ANIMETHEME,
        ];

        Video::factory()
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(
                        AnimeTheme::factory()
                            ->for(Anime::factory())
                            ->state([
                                AnimeTheme::ATTRIBUTE_GROUP => $this->faker->boolean() ? $groupFilter : $excludedGroup,
                            ])
                    )
            )
            ->create();

        $video = Video::with([
            Video::RELATION_ANIMETHEME => function (BelongsTo $query) use ($groupFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_GROUP, $groupFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, new VideoReadQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Show Endpoint shall support constrained eager loading of themes by sequence.
     *
     * @return void
     */
    public function testThemesBySequence(): void
    {
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
            ],
            IncludeParser::param() => Video::RELATION_ANIMETHEME,
        ];

        Video::factory()
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(
                        AnimeTheme::factory()
                            ->for(Anime::factory())
                            ->state([
                                AnimeTheme::ATTRIBUTE_SEQUENCE => $this->faker->boolean() ? $sequenceFilter : $excludedSequence,
                            ])
                    )
            )
            ->create();

        $video = Video::with([
            Video::RELATION_ANIMETHEME => function (BelongsTo $query) use ($sequenceFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, new VideoReadQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Show Endpoint shall support constrained eager loading of themes by type.
     *
     * @return void
     */
    public function testThemesByType(): void
    {
        $typeFilter = ThemeType::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->description,
            ],
            IncludeParser::param() => Video::RELATION_ANIMETHEME,
        ];

        Video::factory()
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(AnimeTheme::factory()->for(Anime::factory()))
            )
            ->create();

        $video = Video::with([
            Video::RELATION_ANIMETHEME => function (BelongsTo $query) use ($typeFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, new VideoReadQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Show Endpoint shall support constrained eager loading of anime by season.
     *
     * @return void
     */
    public function testAnimeBySeason(): void
    {
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->description,
            ],
            IncludeParser::param() => Video::RELATION_ANIME,
        ];

        Video::factory()
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(AnimeTheme::factory()->for(Anime::factory()))
            )
            ->create();

        $video = Video::with([
            Video::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, new VideoReadQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Show Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function testAnimeByYear(): void
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => Video::RELATION_ANIME,
        ];

        Video::factory()
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(
                        AnimeTheme::factory()
                            ->for(
                                Anime::factory()
                                    ->state([
                                        Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                                    ])
                            )
                    )
            )
            ->create();

        $video = Video::with([
            Video::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, new VideoReadQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
