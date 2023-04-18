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
use App\Http\Api\Query\Query;
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
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class AnimeShowTest.
 */
class AnimeShowTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Anime Show Endpoint shall return an Anime Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $anime = Anime::factory()->create();

        $response = $this->get(route('api.anime.show', ['anime' => $anime]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query()))
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
    public function testSoftDelete(): void
    {
        $anime = Anime::factory()->createOne();

        $anime->delete();

        $anime->unsetRelations();

        $response = $this->get(route('api.anime.show', ['anime' => $anime]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query()))
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
    public function testAllowedIncludePaths(): void
    {
        $schema = new AnimeSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $anime = Anime::factory()->jsonApiResource()->createOne();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
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
    public function testSparseFieldsets(): void
    {
        $schema = new AnimeSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AnimeResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $anime = Anime::factory()->create();

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
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
    public function testThemesByGroup(): void
    {
        $groupFilter = $this->faker->word();
        $excludedGroup = $this->faker->word();

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_GROUP => $groupFilter,
            ],
            IncludeParser::param() => Anime::RELATION_THEMES,
        ];

        $anime = Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        [AnimeTheme::ATTRIBUTE_GROUP => $groupFilter],
                        [AnimeTheme::ATTRIBUTE_GROUP => $excludedGroup],
                    ))
            )
            ->createOne();

        $anime->unsetRelations()->load([
            Anime::RELATION_THEMES => function (HasMany $query) use ($groupFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_GROUP, $groupFilter);
            },
        ]);

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
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
    public function testThemesBySequence(): void
    {
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
            ],
            IncludeParser::param() => Anime::RELATION_THEMES,
        ];

        $anime = Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        [AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
                        [AnimeTheme::ATTRIBUTE_SEQUENCE => $excludedSequence],
                    ))
            )
            ->createOne();

        $anime->unsetRelations()->load([
            Anime::RELATION_THEMES => function (HasMany $query) use ($sequenceFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
            },
        ]);

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
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
    public function testThemesByType(): void
    {
        $typeFilter = ThemeType::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->description,
            ],
            IncludeParser::param() => Anime::RELATION_THEMES,
        ];

        $anime = Anime::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull()))
            ->createOne();

        $anime->unsetRelations()->load([
            Anime::RELATION_THEMES => function (HasMany $query) use ($typeFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
            },
        ]);

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
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
    public function testEntriesByNsfw(): void
    {
        $nsfwFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_NSFW => $nsfwFilter,
            ],
            IncludeParser::param() => Anime::RELATION_ENTRIES,
        ];

        $anime = Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->has(AnimeThemeEntry::factory()->count($this->faker->numberBetween(1, 3)))
                    ->count($this->faker->numberBetween(1, 3))
            )
            ->createOne();

        $anime->unsetRelations()->load([
            Anime::RELATION_ENTRIES => function (HasMany $query) use ($nsfwFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter);
            },
        ]);

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
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
    public function testEntriesBySpoiler(): void
    {
        $spoilerFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_SPOILER => $spoilerFilter,
            ],
            IncludeParser::param() => Anime::RELATION_ENTRIES,
        ];

        $anime = Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->has(AnimeThemeEntry::factory()->count($this->faker->numberBetween(1, 3)))
                    ->count($this->faker->numberBetween(1, 3))
            )
            ->createOne();

        $anime->unsetRelations()->load([
            Anime::RELATION_ENTRIES => function (HasMany $query) use ($spoilerFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter);
            },
        ]);

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
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
    public function testEntriesByVersion(): void
    {
        $versionFilter = $this->faker->numberBetween(1, 3);
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
            ],
            IncludeParser::param() => Anime::RELATION_ENTRIES,
        ];

        $anime = Anime::factory()
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
            ->createOne();

        $anime->unsetRelations()->load([
            Anime::RELATION_ENTRIES => function (HasMany $query) use ($versionFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter);
            },
        ]);

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
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
    public function testResourcesBySite(): void
    {
        $siteFilter = ResourceSite::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                ExternalResource::ATTRIBUTE_SITE => $siteFilter->description,
            ],
            IncludeParser::param() => Anime::RELATION_RESOURCES,
        ];

        $anime = Anime::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull()), Anime::RELATION_RESOURCES)
            ->createOne();

        $anime->unsetRelations()->load([
            Anime::RELATION_RESOURCES => function (BelongsToMany $query) use ($siteFilter) {
                $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
            },
        ]);

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
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
    public function testImagesByFacet(): void
    {
        $facetFilter = ImageFacet::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Image::ATTRIBUTE_FACET => $facetFilter->description,
            ],
            IncludeParser::param() => Anime::RELATION_IMAGES,
        ];

        $anime = Anime::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            ->createOne();

        $anime->unsetRelations()->load([
            Anime::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
            },
        ]);

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
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
    public function testVideosByLyrics(): void
    {
        $lyricsFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_LYRICS => $lyricsFilter,
            ],
            IncludeParser::param() => Anime::RELATION_VIDEOS,
        ];

        $anime = Anime::factory()->jsonApiResource()->createOne();

        $anime->unsetRelations()->load([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($lyricsFilter) {
                $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
            },
        ]);

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
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
    public function testVideosByNc(): void
    {
        $ncFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_NC => $ncFilter,
            ],
            IncludeParser::param() => Anime::RELATION_VIDEOS,
        ];

        $anime = Anime::factory()->jsonApiResource()->createOne();

        $anime->unsetRelations()->load([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($ncFilter) {
                $query->where(Video::ATTRIBUTE_NC, $ncFilter);
            },
        ]);

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
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
    public function testVideosByOverlap(): void
    {
        $overlapFilter = VideoOverlap::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_OVERLAP => $overlapFilter->description,
            ],
            IncludeParser::param() => Anime::RELATION_VIDEOS,
        ];

        $anime = Anime::factory()->jsonApiResource()->createOne();

        $anime->unsetRelations()->load([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($overlapFilter) {
                $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
            },
        ]);

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
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
    public function testVideosByResolution(): void
    {
        $resolutionFilter = $this->faker->randomNumber();
        $excludedResolution = $resolutionFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_RESOLUTION => $resolutionFilter,
            ],
            IncludeParser::param() => Anime::RELATION_VIDEOS,
        ];

        $anime = Anime::factory()
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

        $anime->unsetRelations()->load([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($resolutionFilter) {
                $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
            },
        ]);

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
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
    public function testVideosBySource(): void
    {
        $sourceFilter = VideoSource::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_SOURCE => $sourceFilter->description,
            ],
            IncludeParser::param() => Anime::RELATION_VIDEOS,
        ];

        $anime = Anime::factory()->jsonApiResource()->createOne();

        $anime->unsetRelations()->load([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($sourceFilter) {
                $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
            },
        ]);

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
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
    public function testVideosBySubbed(): void
    {
        $subbedFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_SUBBED => $subbedFilter,
            ],
            IncludeParser::param() => Anime::RELATION_VIDEOS,
        ];

        $anime = Anime::factory()->jsonApiResource()->createOne();

        $anime->unsetRelations()->load([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($subbedFilter) {
                $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
            },
        ]);

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
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
    public function testVideosByUncen(): void
    {
        $uncenFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_UNCEN => $uncenFilter,
            ],
            IncludeParser::param() => Anime::RELATION_VIDEOS,
        ];

        $anime = Anime::factory()->jsonApiResource()->createOne();

        $anime->unsetRelations()->load([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($uncenFilter) {
                $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
            },
        ]);

        $response = $this->get(route('api.anime.show', ['anime' => $anime] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeResource($anime, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
