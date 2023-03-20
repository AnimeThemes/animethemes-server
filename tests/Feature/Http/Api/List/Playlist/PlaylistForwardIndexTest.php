<?php

declare(strict_types=1);

namespace Http\Api\List\Playlist;

use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Auth\CrudPermission;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\List\PlaylistVisibility;
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
use App\Http\Api\Schema\List\Playlist\ForwardBackwardSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\List\Playlist\Collection\TrackCollection;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Collection;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistForwardIndexTest.
 */
class PlaylistForwardIndexTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Playlist Forward Index Endpoint shall forbid a private playlist from being publicly viewed.
     *
     * @return void
     */
    public function testPrivatePlaylistCannotBePubliclyViewed(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE,
            ]);

        $response = $this->get(route('api.playlist.forward', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Forward Index Endpoint shall forbid the user from viewing private playlist tracks if not owned.
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

        $response = $this->get(route('api.playlist.forward', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Forward Index Endpoint shall allow private playlist tracks to be viewed by the owner.
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

        $response = $this->get(route('api.playlist.forward', ['playlist' => $playlist]));

        $response->assertOk();
    }

    /**
     * The Playlist Forward Index Endpoint shall allow unlisted playlist tracks to be viewed.
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

        $response = $this->get(route('api.playlist.forward', ['playlist' => $playlist]));

        $response->assertOk();
    }

    /**
     * The Playlist Forward Index Endpoint shall allow public playlist tracks to be viewed.
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

        $response = $this->get(route('api.playlist.forward', ['playlist' => $playlist]));

        $response->assertOk();
    }

    /**
     * By default, the Forward Index Endpoint shall return a collection of Track Resources that belong to the Playlist.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $trackCount = $this->faker->numberBetween(2, 9);

        $playlist = Playlist::factory()
            ->tracks($trackCount)
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

        $response = $this->get(route('api.playlist.forward', ['playlist' => $playlist]));

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
     * The Playlist Forward Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        $playlist = Playlist::factory()
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $response = $this->get(route('api.playlist.forward', ['playlist' => $playlist]));

        $response->assertJsonStructure([
            TrackCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Playlist Forward Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new ForwardBackwardSchema();

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
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $response = $this->get(route('api.playlist.forward', ['playlist' => $playlist] + $parameters));

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
     * The Playlist Forward Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new ForwardBackwardSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                TrackResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $playlist = Playlist::factory()
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $response = $this->get(route('api.playlist.forward', ['playlist' => $playlist] + $parameters));

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
     * The Playlist Forward Index Endpoint shall forbid sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new ForwardBackwardSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Direction::getRandomInstance()),
        ];

        $playlist = Playlist::factory()
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $response = $this->get(route('api.playlist.forward', ['playlist' => $playlist] + $parameters));

        $response->assertJsonValidationErrors([
            SortParser::param(),
        ]);
    }

    /**
     * The Playlist Forward Index Endpoint shall forbid filter resources.
     *
     * @return void
     */
    public function testFilters(): void
    {
        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_CREATED_AT => $this->faker->date(),
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        $playlist = Playlist::factory()
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $response = $this->get(route('api.playlist.forward', ['playlist' => $playlist] + $parameters));

        $response->assertJsonValidationErrors([
            FilterParser::param(),
        ]);
    }
}
