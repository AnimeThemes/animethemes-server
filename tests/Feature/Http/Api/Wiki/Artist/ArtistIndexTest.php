<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Artist;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
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
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Models\BaseModel;
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
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Class ArtistIndexTest.
 */
class ArtistIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Artist Index Endpoint shall return a collection of Artist Resources.
     *
     * @return void
     */
    public function test_default(): void
    {
        $artists = Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.artist.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ArtistCollection($artists, new Query())
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
    public function test_paginated(): void
    {
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
    public function test_allowed_include_paths(): void
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
                    new ArtistCollection($artists, new Query($parameters))
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
    public function test_sparse_fieldsets(): void
    {
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
                    new ArtistCollection($artists, new Query($parameters))
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
    public function test_sorts(): void
    {
        $schema = new ArtistSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.artist.index', $parameters));

        $artists = $this->sort(Artist::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new ArtistCollection($artists, $query)
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
    public function test_created_at_filter(): void
    {
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
                    new ArtistCollection($artist, new Query($parameters))
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
    public function test_updated_at_filter(): void
    {
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
                    new ArtistCollection($artist, new Query($parameters))
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
    public function test_without_trashed_filter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        Artist::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $artist = Artist::withoutTrashed()->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ArtistCollection($artist, new Query($parameters))
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
    public function test_with_trashed_filter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        Artist::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $artist = Artist::withTrashed()->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ArtistCollection($artist, new Query($parameters))
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
    public function test_only_trashed_filter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Artist::factory()->count($this->faker->randomDigitNotNull())->create();

        Artist::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $artist = Artist::onlyTrashed()->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ArtistCollection($artist, new Query($parameters))
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
    public function test_deleted_at_filter(): void
    {
        $deletedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_DELETED_AT => $deletedFilter,
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            Artist::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Artist::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        $artist = Artist::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ArtistCollection($artist, new Query($parameters))
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
    public function test_themes_by_sequence(): void
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
                    new ArtistCollection($artists, new Query($parameters))
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
    public function test_themes_by_type(): void
    {
        $typeFilter = Arr::random(ThemeType::cases());

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->localize(),
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
                    new ArtistCollection($artists, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Index Endpoint shall support constrained eager loading of anime by media format.
     *
     * @return void
     */
    public function test_anime_by_media_format(): void
    {
        $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
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
            Artist::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
                $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
            },
        ])
            ->get();

        $response = $this->get(route('api.artist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ArtistCollection($artists, new Query($parameters))
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
    public function test_anime_by_season(): void
    {
        $seasonFilter = Arr::random(AnimeSeason::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
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
                    new ArtistCollection($artists, new Query($parameters))
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
    public function test_anime_by_year(): void
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
                    new ArtistCollection($artists, new Query($parameters))
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
    public function test_resources_by_site(): void
    {
        $siteFilter = Arr::random(ResourceSite::cases());

        $parameters = [
            FilterParser::param() => [
                ExternalResource::ATTRIBUTE_SITE => $siteFilter->localize(),
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
                    new ArtistCollection($artists, new Query($parameters))
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
    public function test_images_by_facet(): void
    {
        $facetFilter = Arr::random(ImageFacet::cases());

        $parameters = [
            FilterParser::param() => [
                Image::ATTRIBUTE_FACET => $facetFilter->localize(),
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
                    new ArtistCollection($artists, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
