<?php

declare(strict_types=1);

namespace Http\Api\Artist;

use App\Enums\AnimeSeason;
use App\Enums\ImageFacet;
use App\Enums\ResourceSite;
use App\Enums\ThemeType;
use App\Http\Resources\ArtistResource;
use App\Http\Api\QueryParser;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Models\Image;
use App\Models\Song;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class ArtistShowTest.
 */
class ArtistShowTest extends TestCase
{
    use RefreshDatabase;
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
                    ArtistResource::make($artist, QueryParser::make())
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
                    ArtistResource::make($artist, QueryParser::make())
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
        $allowedPaths = collect(ArtistResource::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Artist::factory()->jsonApiResource()->create();
        $artist = Artist::with($includedPaths->all())->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
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

        $fields = collect([
            'id',
            'name',
            'slug',
            'as',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                ArtistResource::$wrap => $includedFields->join(','),
            ],
        ];

        $artist = Artist::factory()->create();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'group' => $groupFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'songs.themes',
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull)
                            ->state(new Sequence(
                                ['group' => $groupFilter],
                                ['group' => $excludedGroup],
                            ))
                    )
            )
            ->create();

        $artist = Artist::with([
            'songs.themes' => function (HasMany $query) use ($groupFilter) {
                $query->where('group', $groupFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
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
        $sequenceFilter = $this->faker->randomDigitNotNull;
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'sequence' => $sequenceFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'songs.themes',
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull)
                            ->state(new Sequence(
                                ['sequence' => $sequenceFilter],
                                ['sequence' => $excludedSequence],
                            ))
                    )
            )
            ->create();

        $artist = Artist::with([
            'songs.themes' => function (HasMany $query) use ($sequenceFilter) {
                $query->where('sequence', $sequenceFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'type' => $typeFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'songs.themes',
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull)
                    )
            )
            ->create();

        $artist = Artist::with([
            'songs.themes' => function (HasMany $query) use ($typeFilter) {
                $query->where('type', $typeFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'season' => $seasonFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'songs.themes.anime',
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull)
                    )
            )
            ->create();

        $artist = Artist::with([
            'songs.themes.anime' => function (BelongsTo $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'year' => $yearFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'songs.themes.anime',
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(
                        Theme::factory()
                            ->for(
                                Anime::factory()
                                    ->state([
                                        'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                                    ])
                            )
                            ->count($this->faker->randomDigitNotNull)
                    )
            )
            ->create();

        $artist = Artist::with([
            'songs.themes.anime' => function (BelongsTo $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'site' => $siteFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'externalResources',
        ];

        Artist::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $artist = Artist::with([
            'externalResources' => function (BelongsToMany $query) use ($siteFilter) {
                $query->where('site', $siteFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'facet' => $facetFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'images',
        ];

        Artist::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $artist = Artist::with([
            'images' => function (BelongsToMany $query) use ($facetFilter) {
                $query->where('facet', $facetFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
