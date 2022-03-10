<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Artist;

use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Wiki\ArtistQuery;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class ArtistIndexTest.
 */
class ArtistIndexTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Artist Index Endpoint shall return a collection of Artist Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $this->withoutEvents();

        $artists = Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.artist.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, new ArtistQuery())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        $this->withoutEvents();

        Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.artist.index'));

        $response->assertJsonStructure([
            ArtistCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Artist Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new ArtistSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Artist::factory()->jsonApiResource()->create();
        $artists = Artist::with($includedPaths->all())->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, new ArtistQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $this->withoutEvents();

        $schema = new ArtistSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                ArtistResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $artists = Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, new ArtistQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $this->withoutEvents();

        $schema = new ArtistSchema();

        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Direction::getRandomInstance()),
        ];

        $query = new ArtistQuery($parameters);

        Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    $query->collection($query->index())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support filtering by created_at.
     *
     * @return void
     */
    public function testCreatedAtFilter(): void
    {
        $this->withoutEvents();

        $createdFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_CREATED_AT => $createdFilter,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($createdFilter, function () {
            Artist::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Artist::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $artist = Artist::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, new ArtistQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support filtering by updated_at.
     *
     * @return void
     */
    public function testUpdatedAtFilter(): void
    {
        $this->withoutEvents();

        $updatedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_UPDATED_AT => $updatedFilter,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($updatedFilter, function () {
            Artist::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Artist::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $artist = Artist::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, new ArtistQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter(): void
    {
        $this->withoutEvents();

        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteArtist = Artist::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteArtist->each(function (Artist $artist) {
            $artist->delete();
        });

        $artist = Artist::withoutTrashed()->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, new ArtistQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter(): void
    {
        $this->withoutEvents();

        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteArtist = Artist::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteArtist->each(function (Artist $artist) {
            $artist->delete();
        });

        $artist = Artist::withTrashed()->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, new ArtistQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter(): void
    {
        $this->withoutEvents();

        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteArtist = Artist::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteArtist->each(function (Artist $artist) {
            $artist->delete();
        });

        $artist = Artist::onlyTrashed()->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, new ArtistQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support filtering by deleted_at.
     *
     * @return void
     */
    public function testDeletedAtFilter(): void
    {
        $this->withoutEvents();

        $deletedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_DELETED_AT => $deletedFilter,
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            $artists = Artist::factory()->count($this->faker->randomDigitNotNull())->create();
            $artists->each(function (Artist $artist) {
                $artist->delete();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            $artists = Artist::factory()->count($this->faker->randomDigitNotNull())->create();
            $artists->each(function (Artist $artist) {
                $artist->delete();
            });
        });

        $artist = Artist::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artist, new ArtistQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support constrained eager loading of themes by group.
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
            IncludeParser::param() => Artist::RELATION_ANIMETHEMES,
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
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $artists = Artist::with([
            Artist::RELATION_ANIMETHEMES => function (HasMany $query) use ($groupFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_GROUP, $groupFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, new ArtistQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support constrained eager loading of themes by sequence.
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
            IncludeParser::param() => Artist::RELATION_ANIMETHEMES,
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
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $artists = Artist::with([
            Artist::RELATION_ANIMETHEMES => function (HasMany $query) use ($sequenceFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, new ArtistQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support constrained eager loading of themes by type.
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
            IncludeParser::param() => Artist::RELATION_ANIMETHEMES,
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
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $artists = Artist::with([
            Artist::RELATION_ANIMETHEMES => function (HasMany $query) use ($typeFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, new ArtistQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support constrained eager loading of anime by season.
     *
     * @return void
     */
    public function testAnimeBySeason(): void
    {
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->description,
            ],
            IncludeParser::param() => Artist::RELATION_ANIME,
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
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $artists = Artist::with([
            Artist::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, new ArtistQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function testAnimeByYear(): void
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => Artist::RELATION_ANIME,
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
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $artists = Artist::with([
            Artist::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, new ArtistQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support constrained eager loading of resources by site.
     *
     * @return void
     */
    public function testResourcesBySite(): void
    {
        $this->withoutEvents();

        $siteFilter = ResourceSite::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                ExternalResource::ATTRIBUTE_SITE => $siteFilter->description,
            ],
            IncludeParser::param() => Artist::RELATION_RESOURCES,
        ];

        Artist::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull()), Artist::RELATION_RESOURCES)
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $artists = Artist::with([
            Artist::RELATION_RESOURCES => function (BelongsToMany $query) use ($siteFilter) {
                $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, new ArtistQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support constrained eager loading of images by facet.
     *
     * @return void
     */
    public function testImagesByFacet(): void
    {
        $this->withoutEvents();

        $facetFilter = ImageFacet::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Image::ATTRIBUTE_FACET => $facetFilter->description,
            ],
            IncludeParser::param() => Artist::RELATION_IMAGES,
        ];

        Artist::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $artists = Artist::with([
            Artist::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ArtistCollection::make($artists, new ArtistQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
