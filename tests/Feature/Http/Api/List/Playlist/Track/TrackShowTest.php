<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist\Track;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\List\Playlist\TrackSchema;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class TrackShowTest.
 */
class TrackShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Track Show Endpoint shall forbid a private playlist from being publicly viewed.
     *
     * @return void
     */
    public function testPrivatePlaylistTrackCannotBePubliclyViewed(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE,
            ]);

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $response = $this->get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track]));

        $response->assertForbidden();
    }

    /**
     * The Track Show Endpoint shall forbid the user from viewing a private playlist track if not owned.
     *
     * @return void
     */
    public function testPrivatePlaylistTrackCannotBePubliclyViewedIfNotOwned(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE,
            ]);

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $user = User::factory()->withPermission(CrudPermission::VIEW()->format(PlaylistTrack::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track]));

        $response->assertForbidden();
    }

    /**
     * The Track Show Endpoint shall allow a private playlist track to be viewed by the owner.
     *
     * @return void
     */
    public function testPrivatePlaylistTrackCanBeViewedByOwner(): void
    {
        $user = User::factory()->withPermission(CrudPermission::VIEW()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE,
            ]);

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track]));

        $response->assertOk();
    }

    /**
     * The Track Show Endpoint shall allow an unlisted playlist track to be viewed.
     *
     * @return void
     */
    public function testUnlistedPlaylistTrackCanBeViewed(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::UNLISTED,
            ]);

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $response = $this->get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track]));

        $response->assertOk();
    }

    /**
     * The Track Show Endpoint shall allow a public playlist track to be viewed.
     *
     * @return void
     */
    public function testPublicPlaylistTrackCanBeViewed(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $response = $this->get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track]));

        $response->assertOk();
    }

    /**
     * The Track Show Endpoint shall scope bindings.
     *
     * @return void
     */
    public function testScoped(): void
    {
        $user = User::factory()->withPermission(CrudPermission::VIEW()->format(PlaylistTrack::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->has(PlaylistTrack::factory()->count($this->faker->randomDigitNotNull()), Playlist::RELATION_TRACKS)
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory()->for(User::factory()))
            ->createOne();

        $response = $this->get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track]));

        $response->assertNotFound();
    }

    /**
     * By default, the Track Show Endpoint shall return a Track Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $playlist = Playlist::factory()
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $response = $this->get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track]));

        $track->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TrackResource($track, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Track Show Endpoint shall return a Track Resource for soft deleted playlist tracks.
     *
     * @return void
     */
    public function testSoftDelete(): void
    {
        $playlist = Playlist::factory()
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $track->delete();

        $response = $this->get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track]));

        $track->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TrackResource($track, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Track Show Endpoint shall allow inclusion of related resources.
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
        ];

        $playlist = Playlist::factory()
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->for(PlaylistTrack::factory()->for($playlist), PlaylistTrack::RELATION_PREVIOUS)
            ->for(PlaylistTrack::factory()->for($playlist), PlaylistTrack::RELATION_NEXT)
            ->createOne();

        $response = $this->get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $track->unsetRelations()->load($includedPaths->all());

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TrackResource($track, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Track Show Endpoint shall implement sparse fieldsets.
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
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $response = $this->get(route('api.playlist.track.show', ['playlist' => $playlist, 'track' => $track] + $parameters));

        $track->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new TrackResource($track, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
