<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Artist;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Models\Wiki\Theme;
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
                    ArtistResource::make($artist, Query::make())
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
                    ArtistResource::make($artist, Query::make())
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
            IncludeParser::$param => $includedPaths->join(','),
        ];

        Artist::factory()->jsonApiResource()->create();
        $artist = Artist::with($includedPaths->all())->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, Query::make($parameters))
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
            FieldParser::$param => [
                ArtistResource::$wrap => $includedFields->join(','),
            ],
        ];

        $artist = Artist::factory()->create();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, Query::make($parameters))
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
                'group' => $groupFilter,
            ],
            IncludeParser::$param => 'songs.themes',
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull())
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
                    ArtistResource::make($artist, Query::make($parameters))
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
                'sequence' => $sequenceFilter,
            ],
            IncludeParser::$param => 'songs.themes',
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull())
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
                    ArtistResource::make($artist, Query::make($parameters))
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
                'type' => $typeFilter->key,
            ],
            IncludeParser::$param => 'songs.themes',
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull())
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
                    ArtistResource::make($artist, Query::make($parameters))
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
                'season' => $seasonFilter->key,
            ],
            IncludeParser::$param => 'songs.themes.anime',
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->count($this->faker->randomDigitNotNull())
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
                    ArtistResource::make($artist, Query::make($parameters))
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
                'year' => $yearFilter,
            ],
            IncludeParser::$param => 'songs.themes.anime',
        ];

        Artist::factory()
            ->has(
                Song::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        Theme::factory()
                            ->for(
                                Anime::factory()
                                    ->state([
                                        'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                                    ])
                            )
                            ->count($this->faker->randomDigitNotNull())
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
                    ArtistResource::make($artist, Query::make($parameters))
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
                'site' => $siteFilter->key,
            ],
            IncludeParser::$param => 'resources',
        ];

        Artist::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull()), 'resources')
            ->create();

        $artist = Artist::with([
            'resources' => function (BelongsToMany $query) use ($siteFilter) {
                $query->where('site', $siteFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.artist.show', ['artist' => $artist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistResource::make($artist, Query::make($parameters))
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
                'facet' => $facetFilter->key,
            ],
            IncludeParser::$param => 'images',
        ];

        Artist::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
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
                    ArtistResource::make($artist, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
