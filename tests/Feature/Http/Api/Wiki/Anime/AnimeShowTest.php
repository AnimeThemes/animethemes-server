<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime;

use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\ThemeType;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query;
use App\Http\Api\Schema\Wiki\AnimeSchema;
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
        $schema = new AnimeSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

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

        $schema = new AnimeSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::$param => [
                AnimeResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
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
                AnimeTheme::ATTRIBUTE_GROUP => $groupFilter,
            ],
            IncludeParser::$param => Anime::RELATION_THEMES,
        ];

        Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        [AnimeTheme::ATTRIBUTE_GROUP => $groupFilter],
                        [AnimeTheme::ATTRIBUTE_GROUP => $excludedGroup],
                    ))
            )
            ->create();

        $anime = Anime::with([
            Anime::RELATION_THEMES => function (HasMany $query) use ($groupFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_GROUP, $groupFilter);
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
                AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
            ],
            IncludeParser::$param => Anime::RELATION_THEMES,
        ];

        Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        [AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
                        [AnimeTheme::ATTRIBUTE_SEQUENCE => $excludedSequence],
                    ))
            )
            ->create();

        $anime = Anime::with([
            Anime::RELATION_THEMES => function (HasMany $query) use ($sequenceFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
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
                AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->description,
            ],
            IncludeParser::$param => Anime::RELATION_THEMES,
        ];

        Anime::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $anime = Anime::with([
            Anime::RELATION_THEMES => function (HasMany $query) use ($typeFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
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
                AnimeThemeEntry::ATTRIBUTE_NSFW => $nsfwFilter,
            ],
            IncludeParser::$param => Anime::RELATION_ENTRIES,
        ];

        Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->has(AnimeThemeEntry::factory()->count($this->faker->numberBetween(1, 3)))
                    ->count($this->faker->numberBetween(1, 3))
            )
            ->create();

        $anime = Anime::with([
            Anime::RELATION_ENTRIES => function (HasMany $query) use ($nsfwFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter);
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
                AnimeThemeEntry::ATTRIBUTE_SPOILER => $spoilerFilter,
            ],
            IncludeParser::$param => Anime::RELATION_ENTRIES,
        ];

        Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->has(AnimeThemeEntry::factory()->count($this->faker->numberBetween(1, 3)))
                    ->count($this->faker->numberBetween(1, 3))
            )
            ->create();

        $anime = Anime::with([
            Anime::RELATION_ENTRIES => function (HasMany $query) use ($spoilerFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter);
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
                AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
            ],
            IncludeParser::$param => Anime::RELATION_ENTRIES,
        ];

        Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->has(
                        AnimeThemeEntry::factory()
                            ->count($this->faker->numberBetween(1, 3))
                            ->state(new Sequence(
                                [AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter],
                                [AnimeThemeEntry::ATTRIBUTE_VERSION => $excludedVersion],
                            ))
                    )
            )
            ->create();

        $anime = Anime::with([
            Anime::RELATION_ENTRIES => function (HasMany $query) use ($versionFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter);
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
                ExternalResource::ATTRIBUTE_SITE => $siteFilter->description,
            ],
            IncludeParser::$param => Anime::RELATION_RESOURCES,
        ];

        Anime::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull()), Anime::RELATION_RESOURCES)
            ->create();

        $anime = Anime::with([
            Anime::RELATION_RESOURCES => function (BelongsToMany $query) use ($siteFilter) {
                $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
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
                Image::ATTRIBUTE_FACET => $facetFilter->description,
            ],
            IncludeParser::$param => Anime::RELATION_IMAGES,
        ];

        Anime::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $anime = Anime::with([
            Anime::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
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
                Video::ATTRIBUTE_LYRICS => $lyricsFilter,
            ],
            IncludeParser::$param => Anime::RELATION_VIDEOS,
        ];

        Anime::factory()->jsonApiResource()->create();

        $anime = Anime::with([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($lyricsFilter) {
                $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
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
                Video::ATTRIBUTE_NC => $ncFilter,
            ],
            IncludeParser::$param => Anime::RELATION_VIDEOS,
        ];

        Anime::factory()->jsonApiResource()->create();

        $anime = Anime::with([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($ncFilter) {
                $query->where(Video::ATTRIBUTE_NC, $ncFilter);
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
                Video::ATTRIBUTE_OVERLAP => $overlapFilter->description,
            ],
            IncludeParser::$param => Anime::RELATION_VIDEOS,
        ];

        Anime::factory()->jsonApiResource()->create();

        $anime = Anime::with([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($overlapFilter) {
                $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
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
                Video::ATTRIBUTE_RESOLUTION => $resolutionFilter,
            ],
            IncludeParser::$param => Anime::RELATION_VIDEOS,
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
                                        [Video::ATTRIBUTE_RESOLUTION => $resolutionFilter],
                                        [Video::ATTRIBUTE_RESOLUTION => $excludedResolution],
                                    ))
                            )
                    )
            )
            ->create();

        $anime = Anime::with([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($resolutionFilter) {
                $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
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
                Video::ATTRIBUTE_SOURCE => $sourceFilter->description,
            ],
            IncludeParser::$param => Anime::RELATION_VIDEOS,
        ];

        Anime::factory()->jsonApiResource()->create();

        $anime = Anime::with([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($sourceFilter) {
                $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
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
                Video::ATTRIBUTE_SUBBED => $subbedFilter,
            ],
            IncludeParser::$param => Anime::RELATION_VIDEOS,
        ];

        Anime::factory()->jsonApiResource()->create();

        $anime = Anime::with([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($subbedFilter) {
                $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
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
                Video::ATTRIBUTE_UNCEN => $uncenFilter,
            ],
            IncludeParser::$param => Anime::RELATION_VIDEOS,
        ];

        Anime::factory()->jsonApiResource()->create();

        $anime = Anime::with([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($uncenFilter) {
                $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
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
