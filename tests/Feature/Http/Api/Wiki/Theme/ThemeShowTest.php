<?php

declare(strict_types=1);

namespace Http\Api\Wiki\Theme;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\QueryParser;
use App\Http\Resources\Wiki\Resource\ThemeResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class ThemeShowTest.
 */
class ThemeShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * By default, the Theme Show Endpoint shall return a Theme Resource.
     *
     * @return void
     */
    public function testDefault()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->createOne();

        $theme->unsetRelations();

        $response = $this->get(route('api.theme.show', ['theme' => $theme]));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall return an Theme Theme for soft deleted themes.
     *
     * @return void
     */
    public function testSoftDelete()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->createOne();

        $theme->delete();

        $theme->unsetRelations();

        $response = $this->get(route('api.theme.show', ['theme' => $theme]));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(ThemeResource::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->for(Song::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->create();

        $theme = Theme::with($includedPaths->all())->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'type',
            'sequence',
            'group',
            'slug',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                ThemeResource::$wrap => $includedFields->join(','),
            ],
        ];

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->createOne();

        $theme->unsetRelations();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of anime by season.
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
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->create();

        $theme = Theme::with([
            'anime' => function (BelongsTo $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of anime by year.
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
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Theme::factory()
            ->for(
                Anime::factory()
                    ->state([
                        'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
            ->create();

        $theme = Theme::with([
            'anime' => function (BelongsTo $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of images by facet.
     *
     * @return void
     */
    public function testImagesByFacet()
    {
        $facetFilter = ImageFacet::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'facet' => $facetFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'anime.images',
        ];

        Theme::factory()
            ->for(
                Anime::factory()
                    ->has(Image::factory()->count($this->faker->randomDigitNotNull))
            )
            ->create();

        $theme = Theme::with([
            'anime.images' => function (BelongsToMany $query) use ($facetFilter) {
                $query->where('facet', $facetFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of entries by nsfw.
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

        Theme::factory()
            ->for(Anime::factory())
            ->has(Entry::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $theme = Theme::with([
            'entries' => function (HasMany $query) use ($nsfwFilter) {
                $query->where('nsfw', $nsfwFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of entries by spoiler.
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

        Theme::factory()
            ->for(Anime::factory())
            ->has(Entry::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $theme = Theme::with([
            'entries' => function (HasMany $query) use ($spoilerFilter) {
                $query->where('spoiler', $spoilerFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of entries by version.
     *
     * @return void
     */
    public function testEntriesByVersion()
    {
        $versionFilter = $this->faker->randomDigitNotNull;
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'version' => $versionFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->state(new Sequence(
                        ['version' => $versionFilter],
                        ['version' => $excludedVersion],
                    ))
            )
            ->create();

        $theme = Theme::with([
            'entries' => function (HasMany $query) use ($versionFilter) {
                $query->where('version', $versionFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of videos by lyrics.
     *
     * @return void
     */
    public function testVideosByLyrics()
    {
        $lyricsFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'lyrics' => $lyricsFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->create();

        $theme = Theme::with([
            'entries.videos' => function (BelongsToMany $query) use ($lyricsFilter) {
                $query->where('lyrics', $lyricsFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of videos by nc.
     *
     * @return void
     */
    public function testVideosByNc()
    {
        $ncFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'nc' => $ncFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->create();

        $theme = Theme::with([
            'entries.videos' => function (BelongsToMany $query) use ($ncFilter) {
                $query->where('nc', $ncFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of videos by overlap.
     *
     * @return void
     */
    public function testVideosByOverlap()
    {
        $overlapFilter = VideoOverlap::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'overlap' => $overlapFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->create();

        $theme = Theme::with([
            'entries.videos' => function (BelongsToMany $query) use ($overlapFilter) {
                $query->where('overlap', $overlapFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of videos by resolution.
     *
     * @return void
     */
    public function testVideosByResolution()
    {
        $resolutionFilter = $this->faker->randomNumber();
        $excludedResolution = $resolutionFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'resolution' => $resolutionFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(
                        Video::factory()
                            ->count($this->faker->randomDigitNotNull)
                            ->state(new Sequence(
                                ['resolution' => $resolutionFilter],
                                ['resolution' => $excludedResolution],
                            ))
                    )
            )
            ->create();

        $theme = Theme::with([
            'entries.videos' => function (BelongsToMany $query) use ($resolutionFilter) {
                $query->where('resolution', $resolutionFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of videos by source.
     *
     * @return void
     */
    public function testVideosBySource()
    {
        $sourceFilter = VideoSource::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'source' => $sourceFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->create();

        $theme = Theme::with([
            'entries.videos' => function (BelongsToMany $query) use ($sourceFilter) {
                $query->where('source', $sourceFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of videos by subbed.
     *
     * @return void
     */
    public function testVideosBySubbed()
    {
        $subbedFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'subbed' => $subbedFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->create();

        $theme = Theme::with([
            'entries.videos' => function (BelongsToMany $query) use ($subbedFilter) {
                $query->where('subbed', $subbedFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Show Endpoint shall support constrained eager loading of videos by uncen.
     *
     * @return void
     */
    public function testVideosByUncen()
    {
        $uncenFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'uncen' => $uncenFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->create();

        $theme = Theme::with([
            'entries.videos' => function (BelongsToMany $query) use ($uncenFilter) {
                $query->where('uncen', $uncenFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.theme.show', ['theme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
