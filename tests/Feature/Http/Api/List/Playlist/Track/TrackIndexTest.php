<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist\Track;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Auth\CrudPermission;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\List\PlaylistVisibility;
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
use App\Http\Api\Schema\List\Playlist\TrackSchema;
use App\Http\Resources\List\Playlist\Collection\TrackCollection;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class TrackIndexTest.
 */
class TrackIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Track Index Endpoint shall forbid a private playlist from being publicly viewed.
     *
     * @return void
     */
    public function testPrivatePlaylistTrackCannotBePubliclyViewed(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE,
            ]);

        $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Track Index Endpoint shall forbid the user from viewing private playlist tracks if not owned.
     *
     * @return void
     */
    public function testPrivatePlaylistTrackCannotBePubliclyViewedIfNotOwned(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE,
            ]);

        $user = User::factory()->withPermissions(CrudPermission::VIEW()->format(PlaylistTrack::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Track Index Endpoint shall allow private playlist tracks to be viewed by the owner.
     *
     * @return void
     */
    public function testPrivatePlaylistTrackCanBeViewedByOwner(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::VIEW()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE,
            ]);

        Sanctum::actingAs($user);

        $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist]));

        $response->assertOk();
    }

    /**
     * The Track Index Endpoint shall allow unlisted playlist tracks to be viewed.
     *
     * @return void
     */
    public function testUnlistedPlaylistTrackCanBeViewed(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::UNLISTED,
            ]);

        $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist]));

        $response->assertOk();
    }

    /**
     * The Track Index Endpoint shall allow public playlist tracks to be viewed.
     *
     * @return void
     */
    public function testPublicPlaylistTrackCanBeViewed(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist]));

        $response->assertOk();
    }

    /**
     * By default, the Track Index Endpoint shall return a collection of Track Resources that belong to the Playlist.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $trackCount = $this->faker->randomDigitNotNull();

        $playlist = Playlist::factory()
            ->has(PlaylistTrack::factory()->count($trackCount), Playlist::RELATION_TRACKS)
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        Collection::times(
            $this->faker->randomDigitNotNull(),
            fn () => Playlist::factory()
                ->has(PlaylistTrack::factory()->count($trackCount), Playlist::RELATION_TRACKS)
                ->createOne([
                    Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
                ])
        );

        $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist]));

        $response->assertJsonCount($trackCount, TrackCollection::$wrap);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TrackCollection($playlist->tracks, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Track Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        $playlist = Playlist::factory()
            ->has(PlaylistTrack::factory()->count($this->faker->randomDigitNotNull()), Playlist::RELATION_TRACKS)
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist]));

        $response->assertJsonStructure([
            TrackCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Track Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new TrackSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        $playlist = Playlist::factory()
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->for(PlaylistTrack::factory()->for($playlist), PlaylistTrack::RELATION_PREVIOUS)
            ->for(PlaylistTrack::factory()->for($playlist), PlaylistTrack::RELATION_NEXT)
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist] + $parameters));

        $tracks = PlaylistTrack::with($includedPaths->all())->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TrackCollection($tracks, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Track Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new TrackSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                TrackResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $playlist = Playlist::factory()
            ->has(PlaylistTrack::factory()->count($this->faker->randomDigitNotNull()), Playlist::RELATION_TRACKS)
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TrackCollection($playlist->tracks, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Track Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new TrackSchema();

        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Direction::getRandomInstance()),
        ];

        $playlist = Playlist::factory()
            ->has(PlaylistTrack::factory()->count($this->faker->randomDigitNotNull()), Playlist::RELATION_TRACKS)
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $query = new Query($parameters);

        $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist] + $parameters));

        $tracks = $this->sort(PlaylistTrack::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TrackCollection($tracks, $query))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Track Index Endpoint shall support filtering by created_at.
     *
     * @return void
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

        $playlist = Playlist::factory()
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        Carbon::withTestNow(
            $createdFilter,
            fn () => PlaylistTrack::factory()
                ->for($playlist)
                ->count($this->faker->randomDigitNotNull())
                ->create()
        );

        Carbon::withTestNow(
            $excludedDate,
            fn () => PlaylistTrack::factory()
                ->for($playlist)
                ->count($this->faker->randomDigitNotNull())
                ->create()
        );

        $tracks = PlaylistTrack::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TrackCollection($tracks, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Track Index Endpoint shall support filtering by updated_at.
     *
     * @return void
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

        $playlist = Playlist::factory()
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        Carbon::withTestNow(
            $updatedFilter,
            fn () => PlaylistTrack::factory()
                ->for($playlist)
                ->count($this->faker->randomDigitNotNull())
                ->create()
        );

        Carbon::withTestNow(
            $excludedDate,
            fn () => PlaylistTrack::factory()
                ->for($playlist)
                ->count($this->faker->randomDigitNotNull())
                ->create()
        );

        $tracks = PlaylistTrack::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TrackCollection($tracks, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Track Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        $playlist = Playlist::factory()
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $deleteTracks = PlaylistTrack::factory()
            ->for($playlist)
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteTracks->each(function (PlaylistTrack $track) {
            $track->delete();
        });

        $tracks = PlaylistTrack::withoutTrashed()->get();

        $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TrackCollection($tracks, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Track Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        $playlist = Playlist::factory()
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $deleteTracks = PlaylistTrack::factory()
            ->for($playlist)
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteTracks->each(function (PlaylistTrack $track) {
            $track->delete();
        });

        $tracks = PlaylistTrack::withTrashed()->get();

        $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TrackCollection($tracks, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Track Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        $playlist = Playlist::factory()
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $deleteTracks = PlaylistTrack::factory()
            ->for($playlist)
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteTracks->each(function (PlaylistTrack $track) {
            $track->delete();
        });

        $tracks = PlaylistTrack::onlyTrashed()->get();

        $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TrackCollection($tracks, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Track Index Endpoint shall support filtering by deleted_at.
     *
     * @return void
     */
    public function testDeletedAtFilter(): void
    {
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

        $playlist = Playlist::factory()
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        Carbon::withTestNow($deletedFilter, function () use ($playlist) {
            $tracks = PlaylistTrack::factory()
                ->for($playlist)
                ->count($this->faker->randomDigitNotNull())
                ->create();

            $tracks->each(function (PlaylistTrack $track) {
                $track->delete();
            });
        });

        Carbon::withTestNow($excludedDate, function () use ($playlist) {
            $tracks = PlaylistTrack::factory()
                ->for($playlist)
                ->count($this->faker->randomDigitNotNull())
                ->create();

            $tracks->each(function (PlaylistTrack $track) {
                $track->delete();
            });
        });

        $tracks = PlaylistTrack::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.playlist.track.index', ['playlist' => $playlist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TrackCollection($tracks, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
