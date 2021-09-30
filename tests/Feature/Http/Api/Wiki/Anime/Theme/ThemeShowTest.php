<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
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
        $schema = new ThemeSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

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
        $schema = new ThemeSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::$param => [
                ThemeResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
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
                Anime::ATTRIBUTE_SEASON => $seasonFilter->description,
            ],
            IncludeParser::$param => AnimeTheme::RELATION_ANIME,
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->create();

        $theme = AnimeTheme::with([
            AnimeTheme::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
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
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::$param => AnimeTheme::RELATION_ANIME,
        ];

        AnimeTheme::factory()
            ->for(
                Anime::factory()
                    ->state([
                        Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
            ->create();

        $theme = AnimeTheme::with([
            AnimeTheme::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
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
                Image::ATTRIBUTE_FACET => $facetFilter->description,
            ],
            IncludeParser::$param => AnimeTheme::RELATION_IMAGES,
        ];

        AnimeTheme::factory()
            ->for(
                Anime::factory()
                    ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->create();

        $theme = AnimeTheme::with([
            AnimeTheme::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
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
                AnimeThemeEntry::ATTRIBUTE_NSFW => $nsfwFilter,
            ],
            IncludeParser::$param => AnimeTheme::RELATION_ENTRIES,
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(AnimeThemeEntry::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $theme = AnimeTheme::with([
            AnimeTheme::RELATION_ENTRIES => function (HasMany $query) use ($nsfwFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter);
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
                AnimeThemeEntry::ATTRIBUTE_SPOILER => $spoilerFilter,
            ],
            IncludeParser::$param => AnimeTheme::RELATION_ENTRIES,
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(AnimeThemeEntry::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $theme = AnimeTheme::with([
            AnimeTheme::RELATION_ENTRIES => function (HasMany $query) use ($spoilerFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter);
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
                AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
            ],
            IncludeParser::$param => AnimeTheme::RELATION_ENTRIES,
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        [AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter],
                        [AnimeThemeEntry::ATTRIBUTE_VERSION => $excludedVersion],
                    ))
            )
            ->create();

        $theme = AnimeTheme::with([
            AnimeTheme::RELATION_ENTRIES => function (HasMany $query) use ($versionFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter);
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
                Video::ATTRIBUTE_LYRICS => $lyricsFilter,
            ],
            IncludeParser::$param => AnimeTheme::RELATION_VIDEOS,
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
            AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($lyricsFilter) {
                $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
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
                Video::ATTRIBUTE_NC => $ncFilter,
            ],
            IncludeParser::$param => AnimeTheme::RELATION_VIDEOS,
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
            AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($ncFilter) {
                $query->where(Video::ATTRIBUTE_NC, $ncFilter);
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
                Video::ATTRIBUTE_OVERLAP => $overlapFilter->description,
            ],
            IncludeParser::$param => AnimeTheme::RELATION_VIDEOS,
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
            AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($overlapFilter) {
                $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
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
                Video::ATTRIBUTE_RESOLUTION => $resolutionFilter,
            ],
            IncludeParser::$param => AnimeTheme::RELATION_VIDEOS,
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
                                [Video::ATTRIBUTE_RESOLUTION => $resolutionFilter],
                                [Video::ATTRIBUTE_RESOLUTION => $excludedResolution],
                            ))
                    )
            )
            ->create();

        $theme = AnimeTheme::with([
            AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($resolutionFilter) {
                $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
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
                Video::ATTRIBUTE_SOURCE => $sourceFilter->description,
            ],
            IncludeParser::$param => AnimeTheme::RELATION_VIDEOS,
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
            AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($sourceFilter) {
                $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
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
                Video::ATTRIBUTE_SUBBED => $subbedFilter,
            ],
            IncludeParser::$param => AnimeTheme::RELATION_VIDEOS,
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
            AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($subbedFilter) {
                $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
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
                Video::ATTRIBUTE_UNCEN => $uncenFilter,
            ],
            IncludeParser::$param => AnimeTheme::RELATION_VIDEOS,
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
            AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($uncenFilter) {
                $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
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
