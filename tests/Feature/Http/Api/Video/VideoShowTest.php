<?php

namespace Tests\Feature\Http\Api\Video;

use App\Enums\AnimeSeason;
use App\Enums\ThemeType;
use App\Http\Resources\VideoResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VideoShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Video Show Endpoint shall return a Video Resource with all allowed include paths.
     *
     * @return void
     */
    public function testDefault()
    {
        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $video = Video::with(VideoResource::allowedIncludePaths())->first();

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
        $allowed_paths = collect(VideoResource::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $video = Video::with($included_paths->all())->first();

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
        $fields = collect([
            'id',
            'basename',
            'filename',
            'path',
            'size',
            'resolution',
            'nc',
            'subbed',
            'lyrics',
            'uncen',
            'source',
            'overlap',
            'created_at',
            'updated_at',
            'link',
            'views',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                VideoResource::$wrap => $included_fields->join(','),
            ],
        ];

        Video::factory()->create();

        $video = Video::with(VideoResource::allowedIncludePaths())->first();

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
        $nsfw_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'nsfw' => $nsfw_filter,
            ],
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $video = Video::with([
            'entries' => function ($query) use ($nsfw_filter) {
                $query->where('nsfw', $nsfw_filter);
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
        $spoiler_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'spoiler' => $spoiler_filter,
            ],
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $video = Video::with([
            'entries' => function ($query) use ($spoiler_filter) {
                $query->where('spoiler', $spoiler_filter);
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
        $version_filter = $this->faker->randomDigitNotNull;
        $excluded_version = $version_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'version' => $version_filter,
            ],
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Theme::factory()->for(Anime::factory()))
                    ->state(new Sequence(
                        ['version' => $version_filter],
                        ['version' => $excluded_version],
                    ))
            )
            ->create();

        $video = Video::with([
            'entries' => function ($query) use ($version_filter) {
                $query->where('version', $version_filter);
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
        $group_filter = $this->faker->word();
        $excluded_group = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'group' => $group_filter,
            ],
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->state([
                                'group' => $this->faker->boolean() ? $group_filter : $excluded_group,
                            ])
                    )
            )
            ->create();

        $video = Video::with([
            'entries.theme' => function ($query) use ($group_filter) {
                $query->where('group', $group_filter);
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
        $sequence_filter = $this->faker->randomDigitNotNull;
        $excluded_sequence = $sequence_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'sequence' => $sequence_filter,
            ],
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->state([
                                'sequence' => $this->faker->boolean() ? $sequence_filter : $excluded_sequence,
                            ])
                    )
            )
            ->create();

        $video = Video::with([
            'entries.theme' => function ($query) use ($sequence_filter) {
                $query->where('sequence', $sequence_filter);
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
        $type_filter = ThemeType::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'type' => $type_filter->key,
            ],
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $video = Video::with([
            'entries.theme' => function ($query) use ($type_filter) {
                $query->where('type', $type_filter->value);
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
        $season_filter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $season_filter->key,
            ],
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $video = Video::with([
            'entries.theme.anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
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
        $year_filter = intval($this->faker->year());
        $excluded_year = $year_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $year_filter,
            ],
        ];

        Video::factory()
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(
                        Theme::factory()
                            ->for(
                                Anime::factory()
                                    ->state([
                                        'year' => $this->faker->boolean() ? $year_filter : $excluded_year,
                                    ])
                            )
                    )
            )
            ->create();

        $video = Video::with([
            'entries.theme.anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
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
