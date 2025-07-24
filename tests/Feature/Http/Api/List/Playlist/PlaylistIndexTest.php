<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Concerns\Actions\Http\Api\AggregatesFields;
use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Models\Wiki\ImageFacet;
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
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\List\Collection\PlaylistCollection;
use App\Http\Resources\List\Resource\PlaylistResource;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PlaylistIndexTest extends TestCase
{
    use AggregatesFields;
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Playlist Index Endpoint shall return a collection of Playlist Resources with public visibility.
     */
    public function testDefault(): void
    {
        $publicCount = $this->faker->randomDigitNotNull();

        $playlists = Playlist::factory()
            ->count($publicCount)
            ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value]);

        $unlistedCount = $this->faker->randomDigitNotNull();

        Playlist::factory()
            ->count($unlistedCount)
            ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::UNLISTED->value]);

        $privateCount = $this->faker->randomDigitNotNull();

        Playlist::factory()
            ->count($privateCount)
            ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value]);

        $response = $this->get(route('api.playlist.index'));

        $response->assertJsonCount($publicCount, PlaylistCollection::$wrap);

        $response->assertJson(
            json_decode(
                json_encode(
                    new PlaylistCollection($playlists, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Index Endpoint shall be paginated.
     */
    public function testPaginated(): void
    {
        Playlist::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value]);

        $response = $this->get(route('api.playlist.index'));

        $response->assertJsonStructure([
            PlaylistCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Playlist Index Endpoint shall allow inclusion of related resources.
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new PlaylistSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Playlist::factory()
            ->for(User::factory())
            ->has(PlaylistTrack::factory(), Playlist::RELATION_FIRST)
            ->has(PlaylistTrack::factory(), Playlist::RELATION_LAST)
            ->has(PlaylistTrack::factory()->count($this->faker->randomDigitNotNull()), Playlist::RELATION_TRACKS)
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
            ]);

        $playlists = Playlist::with($includedPaths->all())->get();

        $response = $this->get(route('api.playlist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new PlaylistCollection($playlists, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Index Endpoint shall implement sparse fieldsets.
     */
    public function testSparseFieldsets(): void
    {
        $schema = new PlaylistSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                PlaylistResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $playlists = Playlist::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value]);

        $response = $this->get(route('api.playlist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new PlaylistCollection($playlists, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Index Endpoint shall support sorting resources.
     */
    public function testSorts(): void
    {
        $schema = new PlaylistSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        Playlist::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value]);

        $response = $this->get(route('api.playlist.index', $parameters));

        $builder = Playlist::query();
        $this->withAggregates($builder, $query, $schema);
        $playlists = $this->sort($builder, $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new PlaylistCollection($playlists, $query)
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Index Endpoint shall support filtering by created_at.
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
            Playlist::factory()
                ->count($this->faker->randomDigitNotNull())
                ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value]);
        });

        Carbon::withTestNow($excludedDate, function () {
            Playlist::factory()
                ->count($this->faker->randomDigitNotNull())
                ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value]);
        });

        $playlists = Playlist::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.playlist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new PlaylistCollection($playlists, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Index Endpoint shall support filtering by updated_at.
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
            Playlist::factory()
                ->count($this->faker->randomDigitNotNull())
                ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value]);
        });

        Carbon::withTestNow($excludedDate, function () {
            Playlist::factory()
                ->count($this->faker->randomDigitNotNull())
                ->create([Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value]);
        });

        $playlists = Playlist::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.playlist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new PlaylistCollection($playlists, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Index Endpoint shall support constrained eager loading of images by facet.
     */
    public function testImagesByFacet(): void
    {
        $facetFilter = Arr::random(ImageFacet::cases());

        $parameters = [
            FilterParser::param() => [
                Image::ATTRIBUTE_FACET => $facetFilter->localize(),
            ],
            IncludeParser::param() => Playlist::RELATION_IMAGES,
        ];

        Playlist::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
            ]);

        $playlists = Playlist::with([
            Playlist::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
            },
        ])
            ->get();

        $response = $this->get(route('api.playlist.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new PlaylistCollection($playlists, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
