<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Artist;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Wiki\ArtistQuery;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class ArtistShowTest.
 */
class ArtistShowTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Artist Show Endpoint shall return an Artist Resource.
     *
     * @return void
     */
    public function testDefault()
    {
        $this->withoutEvents();

        $artist = Artist::factory()->create();

        $response = $this->get(route('api.artist.show', ['artist' => $artist]));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, ArtistQuery::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall return an Artist Resource for soft deleted images.
     *
     * @return void
     */
    public function testSoftDelete()
    {
        $this->withoutEvents();

        $artist = Artist::factory()->createOne();

        $artist->delete();

        $artist->unsetRelations();

        $response = $this->get(route('api.artist.show', ['artist' => $artist]));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, ArtistQuery::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $schema = new ArtistSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
        ];

        Artist::factory()->jsonApiResource()->create();
        $artist = Artist::with($includedPaths->all())->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, ArtistQuery::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $this->withoutEvents();

        $schema = new ArtistSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::$param => [
                ArtistResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $artist = Artist::factory()->create();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, ArtistQuery::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall support constrained eager loading of themes by group.
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
            IncludeParser::$param => Artist::RELATION_ANIMETHEMES,
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        AnimeTheme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull())
                            ->state(new Sequence(
                                [AnimeTheme::ATTRIBUTE_GROUP => $groupFilter],
                                [AnimeTheme::ATTRIBUTE_GROUP => $excludedGroup],
                            ))
                    )
            )
            ->create();

        $artist = Artist::with([
            Artist::RELATION_ANIMETHEMES => function (HasMany $query) use ($groupFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_GROUP, $groupFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, ArtistQuery::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall support constrained eager loading of themes by sequence.
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
            IncludeParser::$param => Artist::RELATION_ANIMETHEMES,
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        AnimeTheme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull())
                            ->state(new Sequence(
                                [AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
                                [AnimeTheme::ATTRIBUTE_SEQUENCE => $excludedSequence],
                            ))
                    )
            )
            ->create();

        $artist = Artist::with([
            Artist::RELATION_ANIMETHEMES => function (HasMany $query) use ($sequenceFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, ArtistQuery::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall support constrained eager loading of themes by type.
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
            IncludeParser::$param => Artist::RELATION_ANIMETHEMES,
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        AnimeTheme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull())
                    )
            )
            ->create();

        $artist = Artist::with([
            Artist::RELATION_ANIMETHEMES => function (HasMany $query) use ($typeFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, ArtistQuery::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall support constrained eager loading of anime by season.
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
            IncludeParser::$param => Artist::RELATION_ANIME,
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        AnimeTheme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull())
                    )
            )
            ->create();

        $artist = Artist::with([
            Artist::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, ArtistQuery::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall support constrained eager loading of anime by year.
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
            IncludeParser::$param => Artist::RELATION_ANIME,
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        AnimeTheme::factory()
                            ->for(
                                Anime::factory()
                                    ->state([
                                        Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                                    ])
                            )
                            ->count($this->faker->randomDigitNotNull())
                    )
            )
            ->create();

        $artist = Artist::with([
            Artist::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, ArtistQuery::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall support constrained eager loading of resources by site.
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
            IncludeParser::$param => Artist::RELATION_RESOURCES,
        ];

        Artist::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull()), Artist::RELATION_RESOURCES)
            ->create();

        $artist = Artist::with([
            Artist::RELATION_RESOURCES => function (BelongsToMany $query) use ($siteFilter) {
                $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, ArtistQuery::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Show Endpoint shall support constrained eager loading of images by facet.
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
            IncludeParser::$param => Artist::RELATION_IMAGES,
        ];

        Artist::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $artist = Artist::with([
            Artist::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, ArtistQuery::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
