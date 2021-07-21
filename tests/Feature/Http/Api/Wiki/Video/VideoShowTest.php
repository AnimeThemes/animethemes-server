<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\QueryParser;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class VideoShowTest.
 */
class VideoShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * By default, the Video Show Endpoint shall return a Video Resource.
     *
     * @return void
     */
    public function testDefault()
    {
        $this->withoutEvents();

        $video = Video::factory()->create();

        $response = $this->get(route('api.video.show', ['video' => $video]));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Show Endpoint shall return an Video Video for soft deleted videos.
     *
     * @return void
     */
    public function testSoftDelete()
    {
        $this->withoutEvents();

        $video = Video::factory()->createOne();

        $video->delete();

        $response = $this->get(route('api.video.show', ['video' => $video]));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, QueryParser::make())
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
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(VideoResource::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $video = Video::with($includedPaths->all())->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, QueryParser::make($parameters))
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
    public function testSparseFieldsets()
    {
        $this->withoutEvents();

        $fields = collect([
            'id',
            'basename',
            'filename',
            'path',
            'size',
            'mimetype',
            'resolution',
            'nc',
            'subbed',
            'lyrics',
            'uncen',
            'source',
            'overlap',
            'created_at',
            'updated_at',
            'deleted_at',
            'tags',
            'link',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                VideoResource::$wrap => $includedFields->join(','),
            ],
        ];

        $video = Video::factory()->create();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, QueryParser::make($parameters))
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
    public function testEntriesByNsfw()
    {
        $nsfwFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'nsfw' => $nsfwFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries',
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $video = Video::with([
            'entries' => function (BelongsToMany $query) use ($nsfwFilter) {
                $query->where('nsfw', $nsfwFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, QueryParser::make($parameters))
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
    public function testEntriesBySpoiler()
    {
        $spoilerFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'spoiler' => $spoilerFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries',
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $video = Video::with([
            'entries' => function (BelongsToMany $query) use ($spoilerFilter) {
                $query->where('spoiler', $spoilerFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, QueryParser::make($parameters))
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
    public function testEntriesByVersion()
    {
        $versionFilter = $this->faker->randomDigitNotNull();
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'version' => $versionFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries',
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Theme::factory()->for(Anime::factory()))
                    ->state(new Sequence(
                        ['version' => $versionFilter],
                        ['version' => $excludedVersion],
                    ))
            )
            ->create();

        $video = Video::with([
            'entries' => function (BelongsToMany $query) use ($versionFilter) {
                $query->where('version', $versionFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, QueryParser::make($parameters))
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
    public function testThemesByGroup()
    {
        $groupFilter = $this->faker->word();
        $excludedGroup = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'group' => $groupFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.theme',
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->state([
                                'group' => $this->faker->boolean() ? $groupFilter : $excludedGroup,
                            ])
                    )
            )
            ->create();

        $video = Video::with([
            'entries.theme' => function (BelongsTo $query) use ($groupFilter) {
                $query->where('group', $groupFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, QueryParser::make($parameters))
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
    public function testThemesBySequence()
    {
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'sequence' => $sequenceFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.theme',
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->state([
                                'sequence' => $this->faker->boolean() ? $sequenceFilter : $excludedSequence,
                            ])
                    )
            )
            ->create();

        $video = Video::with([
            'entries.theme' => function (BelongsTo $query) use ($sequenceFilter) {
                $query->where('sequence', $sequenceFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, QueryParser::make($parameters))
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
    public function testThemesByType()
    {
        $typeFilter = ThemeType::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'type' => $typeFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.theme',
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $video = Video::with([
            'entries.theme' => function (BelongsTo $query) use ($typeFilter) {
                $query->where('type', $typeFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, QueryParser::make($parameters))
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
    public function testAnimeBySeason()
    {
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $seasonFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.theme.anime',
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $video = Video::with([
            'entries.theme.anime' => function (BelongsTo $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, QueryParser::make($parameters))
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
    public function testAnimeByYear()
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $yearFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.theme.anime',
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(
                        Theme::factory()
                            ->for(
                                Anime::factory()
                                    ->state([
                                        'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                                    ])
                            )
                    )
            )
            ->create();

        $video = Video::with([
            'entries.theme.anime' => function (BelongsTo $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.video.show', ['video' => $video] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoResource::make($video, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
