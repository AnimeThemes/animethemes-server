<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime;

use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\ThemeType;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class AnimeShowTest.
 */
class AnimeShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * By default, the Anime Show Endpoint shall return an Anime Resource.
     *
     * @return void
     */
    public function testDefault()
    {
        $this->withoutEvents();

        $anime = Anime::factory()->create();

        $response = $this->get(route('api.anime.show', ['anime' => $anime]));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall return an Anime Resource for soft deleted anime.
     *
     * @return void
     */
    public function testSoftDelete()
    {
        $this->withoutEvents();

        $anime = Anime::factory()->createOne();

        $anime->delete();

        $anime->unsetRelations();

        $response = $this->get(route('api.anime.show', ['anime' => $anime]));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(AnimeResource::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(1, count($allowedPaths)));

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
        ];

        Anime::factory()->jsonApiResource()->create();
        $anime = Anime::with($includedPaths->all())->first();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $this->withoutEvents();

        $fields = collect([
            'id',
            'name',
            'slug',
            'year',
            'season',
            'synopsis',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            FieldParser::$param => [
                AnimeResource::$wrap => $includedFields->join(','),
            ],
        ];

        $anime = Anime::factory()->create();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall support constrained eager loading of themes by group.
     *
     * @return void
     */
    public function testThemesByGroup()
    {
        $groupFilter = $this->faker->word();
        $excludedGroup = $this->faker->word();

        $parameters = [
            FilterParser::$param => [
                'group' => $groupFilter,
            ],
            IncludeParser::$param => 'animethemes',
        ];

        Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        ['group' => $groupFilter],
                        ['group' => $excludedGroup],
                    ))
            )
            ->create();

        $anime = Anime::with([
            'animethemes' => function (HasMany $query) use ($groupFilter) {
                $query->where('group', $groupFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall support constrained eager loading of themes by sequence.
     *
     * @return void
     */
    public function testThemesBySequence()
    {
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'sequence' => $sequenceFilter,
            ],
            IncludeParser::$param => 'animethemes',
        ];

        Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        ['sequence' => $sequenceFilter],
                        ['sequence' => $excludedSequence],
                    ))
            )
            ->create();

        $anime = Anime::with([
            'animethemes' => function (HasMany $query) use ($sequenceFilter) {
                $query->where('sequence', $sequenceFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall support constrained eager loading of themes by type.
     *
     * @return void
     */
    public function testThemesByType()
    {
        $typeFilter = ThemeType::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'type' => $typeFilter->description,
            ],
            IncludeParser::$param => 'animethemes',
        ];

        Anime::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $anime = Anime::with([
            'animethemes' => function (HasMany $query) use ($typeFilter) {
                $query->where('type', $typeFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall support constrained eager loading of entries by nsfw.
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
            IncludeParser::$param => 'animethemes.animethemeentries',
        ];

        Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->has(AnimeThemeEntry::factory()->count($this->faker->numberBetween(1, 3)))
                    ->count($this->faker->numberBetween(1, 3))
            )
            ->create();

        $anime = Anime::with([
            'animethemes.animethemeentries' => function (HasMany $query) use ($nsfwFilter) {
                $query->where('nsfw', $nsfwFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall support constrained eager loading of entries by spoiler.
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
            IncludeParser::$param => 'animethemes.animethemeentries',
        ];

        Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->has(AnimeThemeEntry::factory()->count($this->faker->numberBetween(1, 3)))
                    ->count($this->faker->numberBetween(1, 3))
            )
            ->create();

        $anime = Anime::with([
            'animethemes.animethemeentries' => function (HasMany $query) use ($spoilerFilter) {
                $query->where('spoiler', $spoilerFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall support constrained eager loading of entries by version.
     *
     * @return void
     */
    public function testEntriesByVersion()
    {
        $versionFilter = $this->faker->numberBetween(1, 3);
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'version' => $versionFilter,
            ],
            IncludeParser::$param => 'animethemes.animethemeentries',
        ];

        Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->has(
                        AnimeThemeEntry::factory()
                            ->count($this->faker->numberBetween(1, 3))
                            ->state(new Sequence(
                                ['version' => $versionFilter],
                                ['version' => $excludedVersion],
                            ))
                    )
            )
            ->create();

        $anime = Anime::with([
            'animethemes.animethemeentries' => function (HasMany $query) use ($versionFilter) {
                $query->where('version', $versionFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall support constrained eager loading of resources by site.
     *
     * @return void
     */
    public function testResourcesBySite()
    {
        $this->withoutEvents();

        $siteFilter = ResourceSite::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'site' => $siteFilter->description,
            ],
            IncludeParser::$param => 'resources',
        ];

        Anime::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull()), 'resources')
            ->create();

        $anime = Anime::with([
            'resources' => function (BelongsToMany $query) use ($siteFilter) {
                $query->where('site', $siteFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall support constrained eager loading of images by facet.
     *
     * @return void
     */
    public function testImagesByFacet()
    {
        $this->withoutEvents();

        $facetFilter = ImageFacet::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'facet' => $facetFilter->description,
            ],
            IncludeParser::$param => 'images',
        ];

        Anime::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $anime = Anime::with([
            'images' => function (BelongsToMany $query) use ($facetFilter) {
                $query->where('facet', $facetFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall support constrained eager loading of videos by lyrics.
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
            IncludeParser::$param => 'animethemes.animethemeentries.videos',
        ];

        Anime::factory()->jsonApiResource()->create();

        $anime = Anime::with([
            'animethemes.animethemeentries.videos' => function (BelongsToMany $query) use ($lyricsFilter) {
                $query->where('lyrics', $lyricsFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall support constrained eager loading of videos by nc.
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
            IncludeParser::$param => 'animethemes.animethemeentries.videos',
        ];

        Anime::factory()->jsonApiResource()->create();

        $anime = Anime::with([
            'animethemes.animethemeentries.videos' => function (BelongsToMany $query) use ($ncFilter) {
                $query->where('nc', $ncFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall support constrained eager loading of videos by overlap.
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
            IncludeParser::$param => 'animethemes.animethemeentries.videos',
        ];

        Anime::factory()->jsonApiResource()->create();

        $anime = Anime::with([
            'animethemes.animethemeentries.videos' => function (BelongsToMany $query) use ($overlapFilter) {
                $query->where('overlap', $overlapFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall support constrained eager loading of videos by resolution.
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
            IncludeParser::$param => 'animethemes.animethemeentries.videos',
        ];

        Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->has(
                        AnimeThemeEntry::factory()
                            ->count($this->faker->numberBetween(1, 3))
                            ->has(
                                Video::factory()
                                    ->count($this->faker->numberBetween(1, 3))
                                    ->state(new Sequence(
                                        ['resolution' => $resolutionFilter],
                                        ['resolution' => $excludedResolution],
                                    ))
                            )
                    )
            )
            ->create();

        $anime = Anime::with([
            'animethemes.animethemeentries.videos' => function (BelongsToMany $query) use ($resolutionFilter) {
                $query->where('resolution', $resolutionFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall support constrained eager loading of videos by source.
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
            IncludeParser::$param => 'animethemes.animethemeentries.videos',
        ];

        Anime::factory()->jsonApiResource()->create();

        $anime = Anime::with([
            'animethemes.animethemeentries.videos' => function (BelongsToMany $query) use ($sourceFilter) {
                $query->where('source', $sourceFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall support constrained eager loading of videos by subbed.
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
            IncludeParser::$param => 'animethemes.animethemeentries.videos',
        ];

        Anime::factory()->jsonApiResource()->create();

        $anime = Anime::with([
            'animethemes.animethemeentries.videos' => function (BelongsToMany $query) use ($subbedFilter) {
                $query->where('subbed', $subbedFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Show Endpoint shall support constrained eager loading of videos by uncen.
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
            IncludeParser::$param => 'animethemes.animethemeentries.videos',
        ];

        Anime::factory()->jsonApiResource()->create();

        $anime = Anime::with([
            'animethemes.animethemeentries.videos' => function (BelongsToMany $query) use ($uncenFilter) {
                $query->where('uncen', $uncenFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeResource::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
