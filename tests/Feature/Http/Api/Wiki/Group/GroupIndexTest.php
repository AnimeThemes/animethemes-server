<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Group;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Constants\ModelConstants;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
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
use App\Http\Api\Schema\Wiki\GroupSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Collection\GroupCollection;
use App\Http\Resources\Wiki\Resource\GroupResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Group;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GroupIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Group Index Endpoint shall return a collection of Group Resources.
     */
    public function testDefault(): void
    {
        $groups = Group::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.group.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new GroupCollection($groups, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Group Index Endpoint shall be paginated.
     */
    public function testPaginated(): void
    {
        Group::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.group.index'));

        $response->assertJsonStructure([
            GroupCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Series Index Endpoint shall allow inclusion of related resources.
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new GroupSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Group::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $groups = Group::with($includedPaths->all())->get();

        $response = $this->get(route('api.group.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new GroupCollection($groups, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Group Index Endpoint shall implement sparse fieldsets.
     */
    public function testSparseFieldsets(): void
    {
        $schema = new GroupSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                GroupResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $groups = Group::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.group.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new GroupCollection($groups, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Group Index Endpoint shall support sorting resources.
     */
    public function testSorts(): void
    {
        $schema = new GroupSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        Group::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.group.index', $parameters));

        $groups = $this->sort(Group::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new GroupCollection($groups, $query)
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Group Index Endpoint shall support filtering by created_at.
     */
    public function testCreatedAtFilter(): void
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
            Group::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Group::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $group = Group::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.group.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new GroupCollection($group, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Group Index Endpoint shall support filtering by updated_at.
     */
    public function testUpdatedAtFilter(): void
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
            Group::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Group::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $group = Group::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.group.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new GroupCollection($group, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Group Index Endpoint shall support filtering by trashed.
     */
    public function testWithoutTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Group::factory()->count($this->faker->randomDigitNotNull())->create();

        Group::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $group = Group::withoutTrashed()->get();

        $response = $this->get(route('api.group.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new GroupCollection($group, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Group Index Endpoint shall support filtering by trashed.
     */
    public function testWithTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Group::factory()->count($this->faker->randomDigitNotNull())->create();

        Group::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $group = Group::withTrashed()->get();

        $response = $this->get(route('api.group.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new GroupCollection($group, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Group Index Endpoint shall support filtering by trashed.
     */
    public function testOnlyTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Group::factory()->count($this->faker->randomDigitNotNull())->create();

        Group::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $group = Group::onlyTrashed()->get();

        $response = $this->get(route('api.group.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new GroupCollection($group, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Group Index Endpoint shall support filtering by deleted_at.
     */
    public function testDeletedAtFilter(): void
    {
        $deletedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                ModelConstants::ATTRIBUTE_DELETED_AT => $deletedFilter,
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            Group::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Group::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        $groups = Group::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.group.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new GroupCollection($groups, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Group Index Endpoint shall support constrained eager loading of themes by sequence.
     */
    public function testThemesBySequence(): void
    {
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
            ],
            IncludeParser::param() => Group::RELATION_THEMES,
        ];

        Group::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Anime::factory())
                    ->state(new Sequence(
                        [AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
                        [AnimeTheme::ATTRIBUTE_SEQUENCE => $excludedSequence],
                    ))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $groups = Group::with([
            Group::RELATION_THEMES => function (HasMany $query) use ($sequenceFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
            },
        ])
            ->get();

        $response = $this->get(route('api.group.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new GroupCollection($groups, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Group Index Endpoint shall support constrained eager loading of themes by type.
     */
    public function testThemesByType(): void
    {
        $typeFilter = Arr::random(ThemeType::cases());

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->localize(),
            ],
            IncludeParser::param() => Group::RELATION_THEMES,
        ];

        Group::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $groups = Group::with([
            Group::RELATION_THEMES => function (HasMany $query) use ($typeFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
            },
        ])
            ->get();

        $response = $this->get(route('api.group.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new GroupCollection($groups, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Group Index Endpoint shall support constrained eager loading of anime by media format.
     */
    public function testAnimeByMediaFormat(): void
    {
        $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
            ],
            IncludeParser::param() => Group::RELATION_ANIME,
        ];

        Group::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $groups = Group::with([
            Group::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
                $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
            },
        ])
            ->get();

        $response = $this->get(route('api.group.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new GroupCollection($groups, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Group Index Endpoint shall support constrained eager loading of anime by season.
     */
    public function testAnimeBySeason(): void
    {
        $seasonFilter = Arr::random(AnimeSeason::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
            ],
            IncludeParser::param() => Group::RELATION_ANIME,
        ];

        Group::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $groups = Group::with([
            Group::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ])
            ->get();

        $response = $this->get(route('api.group.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new GroupCollection($groups, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Group Index Endpoint shall support constrained eager loading of anime by year.
     */
    public function testAnimeByYear(): void
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => Group::RELATION_ANIME,
        ];

        Group::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(
                        Anime::factory()
                            ->state([
                                Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                            ])
                    )
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $groups = Group::with([
            Group::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ])
            ->get();

        $response = $this->get(route('api.group.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new GroupCollection($groups, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
