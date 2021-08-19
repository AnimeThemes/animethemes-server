<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
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
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        $theme->unsetRelations();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme]));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make())
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
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        $theme->delete();

        $theme->unsetRelations();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme]));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make())
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
            IncludeParser::$param => $includedPaths->join(','),
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->for(Song::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->create();

        $theme = AnimeTheme::with($includedPaths->all())->first();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make($parameters))
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
            FieldParser::$param => [
                ThemeResource::$wrap => $includedFields->join(','),
            ],
        ];

        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->createOne();

        $theme->unsetRelations();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make($parameters))
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
            FilterParser::$param => [
                'season' => $seasonFilter->description,
            ],
            IncludeParser::$param => 'anime',
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->create();

        $theme = AnimeTheme::with([
            'anime' => function (BelongsTo $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make($parameters))
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
            FilterParser::$param => [
                'year' => $yearFilter,
            ],
            IncludeParser::$param => 'anime',
        ];

        AnimeTheme::factory()
            ->for(
                Anime::factory()
                    ->state([
                        'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
            ->create();

        $theme = AnimeTheme::with([
            'anime' => function (BelongsTo $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make($parameters))
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
            FilterParser::$param => [
                'facet' => $facetFilter->description,
            ],
            IncludeParser::$param => 'anime.images',
        ];

        AnimeTheme::factory()
            ->for(
                Anime::factory()
                    ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->create();

        $theme = AnimeTheme::with([
            'anime.images' => function (BelongsToMany $query) use ($facetFilter) {
                $query->where('facet', $facetFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make($parameters))
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
            FilterParser::$param => [
                'nsfw' => $nsfwFilter,
            ],
            IncludeParser::$param => 'animethemeentries',
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(AnimeThemeEntry::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $theme = AnimeTheme::with([
            'animethemeentries' => function (HasMany $query) use ($nsfwFilter) {
                $query->where('nsfw', $nsfwFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make($parameters))
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
            FilterParser::$param => [
                'spoiler' => $spoilerFilter,
            ],
            IncludeParser::$param => 'animethemeentries',
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(AnimeThemeEntry::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $theme = AnimeTheme::with([
            'animethemeentries' => function (HasMany $query) use ($spoilerFilter) {
                $query->where('spoiler', $spoilerFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make($parameters))
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
        $versionFilter = $this->faker->randomDigitNotNull();
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'version' => $versionFilter,
            ],
            IncludeParser::$param => 'animethemeentries',
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        ['version' => $versionFilter],
                        ['version' => $excludedVersion],
                    ))
            )
            ->create();

        $theme = AnimeTheme::with([
            'animethemeentries' => function (HasMany $query) use ($versionFilter) {
                $query->where('version', $versionFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make($parameters))
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
            FilterParser::$param => [
                'lyrics' => $lyricsFilter,
            ],
            IncludeParser::$param => 'animethemeentries.videos',
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->create();

        $theme = AnimeTheme::with([
            'animethemeentries.videos' => function (BelongsToMany $query) use ($lyricsFilter) {
                $query->where('lyrics', $lyricsFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make($parameters))
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
            FilterParser::$param => [
                'nc' => $ncFilter,
            ],
            IncludeParser::$param => 'animethemeentries.videos',
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->create();

        $theme = AnimeTheme::with([
            'animethemeentries.videos' => function (BelongsToMany $query) use ($ncFilter) {
                $query->where('nc', $ncFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make($parameters))
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
            FilterParser::$param => [
                'overlap' => $overlapFilter->description,
            ],
            IncludeParser::$param => 'animethemeentries.videos',
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->create();

        $theme = AnimeTheme::with([
            'animethemeentries.videos' => function (BelongsToMany $query) use ($overlapFilter) {
                $query->where('overlap', $overlapFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make($parameters))
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
            FilterParser::$param => [
                'resolution' => $resolutionFilter,
            ],
            IncludeParser::$param => 'animethemeentries.videos',
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        Video::factory()
                            ->count($this->faker->randomDigitNotNull())
                            ->state(new Sequence(
                                ['resolution' => $resolutionFilter],
                                ['resolution' => $excludedResolution],
                            ))
                    )
            )
            ->create();

        $theme = AnimeTheme::with([
            'animethemeentries.videos' => function (BelongsToMany $query) use ($resolutionFilter) {
                $query->where('resolution', $resolutionFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make($parameters))
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
            FilterParser::$param => [
                'source' => $sourceFilter->description,
            ],
            IncludeParser::$param => 'animethemeentries.videos',
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->create();

        $theme = AnimeTheme::with([
            'animethemeentries.videos' => function (BelongsToMany $query) use ($sourceFilter) {
                $query->where('source', $sourceFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make($parameters))
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
            FilterParser::$param => [
                'subbed' => $subbedFilter,
            ],
            IncludeParser::$param => 'animethemeentries.videos',
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->create();

        $theme = AnimeTheme::with([
            'animethemeentries.videos' => function (BelongsToMany $query) use ($subbedFilter) {
                $query->where('subbed', $subbedFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make($parameters))
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
            FilterParser::$param => [
                'uncen' => $uncenFilter,
            ],
            IncludeParser::$param => 'animethemeentries.videos',
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->create();

        $theme = AnimeTheme::with([
            'animethemeentries.videos' => function (BelongsToMany $query) use ($uncenFilter) {
                $query->where('uncen', $uncenFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeResource::make($theme, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
