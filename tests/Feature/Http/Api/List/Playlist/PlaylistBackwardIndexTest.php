<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Auth\CrudPermission;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\List\PlaylistVisibility;
use App\Events\List\Playlist\PlaylistCreated;
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
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistBackwardIndexTest.
 */
class PlaylistBackwardIndexTest extends TestCase
{
    use WithFaker;

    /**
     * The Playlist Backward Index Endpoint shall forbid a private playlist from being publicly viewed.
     *
     * @return void
     */
    public function test_private_playlist_cannot_be_publicly_viewed(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
            ]);

        $response = $this->get(route('api.playlist.backward', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Backward Index Endpoint shall forbid the user from viewing private playlist tracks if not owned.
     *
     * @return void
     */
    public function test_private_playlist_track_cannot_be_publicly_viewed_if_not_owned(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
            ]);

        $user = User::factory()->withPermissions(CrudPermission::VIEW->format(PlaylistTrack::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.playlist.backward', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Backward Index Endpoint shall allow private playlist tracks to be viewed by the owner.
     *
     * @return void
     */
    public function test_private_playlist_track_can_be_viewed_by_owner(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $user = User::factory()->withPermissions(CrudPermission::VIEW->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
            ]);

        Sanctum::actingAs($user);

        $response = $this->get(route('api.playlist.backward', ['playlist' => $playlist]));

        $response->assertOk();
    }

    /**
     * The Playlist Backward Index Endpoint shall allow unlisted playlist tracks to be viewed.
     *
     * @return void
     */
    public function test_unlisted_playlist_track_can_be_viewed(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::UNLISTED->value,
            ]);

        $response = $this->get(route('api.playlist.backward', ['playlist' => $playlist]));

        $response->assertOk();
    }

    /**
     * The Playlist Backward Index Endpoint shall allow public playlist tracks to be viewed.
     *
     * @return void
     */
    public function test_public_playlist_track_can_be_viewed(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
            ]);

        $response = $this->get(route('api.playlist.backward', ['playlist' => $playlist]));

        $response->assertOk();
    }

    /**
     * By default, the Backward Index Endpoint shall return a collection of Track Resources that belong to the Playlist.
     *
     * @return void
     */
    public function test_default(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $trackCount = $this->faker->numberBetween(2, 9);

        $playlist = Playlist::factory()
            ->tracks($trackCount)
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
            ]);

        Collection::times(
            $this->faker->randomDigitNotNull(),
            fn () => Playlist::factory()
                ->has(PlaylistTrack::factory()->count($trackCount), Playlist::RELATION_TRACKS)
                ->createOne([
                    Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
                ])
        );

        $response = $this->get(route('api.playlist.backward', ['playlist' => $playlist]));

        $response->assertJsonCount($trackCount, TrackCollection::$wrap);

        $response->assertJson(
            json_decode(
                json_encode(
                    new TrackCollection($playlist->tracks->sortByDesc(PlaylistTrack::ATTRIBUTE_ID), new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Backward Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function test_paginated(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $playlist = Playlist::factory()
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
            ]);

        $response = $this->get(route('api.playlist.backward', ['playlist' => $playlist]));

        $response->assertJsonStructure([
            TrackCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Playlist Backward Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function test_allowed_include_paths(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

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
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
            ]);

        $response = $this->get(route('api.playlist.backward', ['playlist' => $playlist] + $parameters));

        $tracks = PlaylistTrack::with($includedPaths->all())->orderByDesc(PlaylistTrack::ATTRIBUTE_ID)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new TrackCollection($tracks, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Backward Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function test_sparse_fieldsets(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

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
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
            ]);

        $response = $this->get(route('api.playlist.backward', ['playlist' => $playlist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new TrackCollection($playlist->tracks->sortByDesc(PlaylistTrack::ATTRIBUTE_ID), new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Backward Index Endpoint shall forbid sorting resources.
     *
     * @return void
     */
    public function test_sorts(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $schema = new ForwardBackwardSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $playlist = Playlist::factory()
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
            ]);

        $response = $this->get(route('api.playlist.backward', ['playlist' => $playlist] + $parameters));

        $response->assertJsonValidationErrors([
            SortParser::param(),
        ]);
    }

    /**
     * The Playlist Backward Index Endpoint shall forbid filter resources.
     *
     * @return void
     */
    public function test_filters(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

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
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
            ]);

        $response = $this->get(route('api.playlist.backward', ['playlist' => $playlist] + $parameters));

        $response->assertJsonValidationErrors([
            FilterParser::param(),
        ]);
    }
}
