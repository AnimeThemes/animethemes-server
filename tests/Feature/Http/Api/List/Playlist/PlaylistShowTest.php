<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Models\Wiki\ImageFacet;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Resources\List\Resource\PlaylistResource;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistShowTest.
 */
class PlaylistShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Playlist Show Endpoint shall forbid a private playlist from being publicly viewed.
     *
     * @return void
     */
    public function testPrivatePlaylistCannotBePubliclyViewed(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE,
            ]);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Show Endpoint shall forbid the user from viewing a private playlist if not owned.
     *
     * @return void
     */
    public function testPrivatePlaylistCannotBePubliclyIfNotOwned(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE,
            ]);

        $user = User::factory()->withPermissions(CrudPermission::VIEW()->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Show Endpoint shall allow a private playlist to be viewed by the owner.
     *
     * @return void
     */
    public function testPrivatePlaylistCanBeViewedByOwner(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::VIEW()->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE,
            ]);

        Sanctum::actingAs($user);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist]));

        $response->assertOk();
    }

    /**
     * The Playlist Show Endpoint shall allow an unlisted playlist to be viewed.
     *
     * @return void
     */
    public function testUnlistedPlaylistCanBeViewed(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::UNLISTED,
            ]);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist]));

        $response->assertOk();
    }

    /**
     * The Playlist Show Endpoint shall allow a public playlist to be viewed.
     *
     * @return void
     */
    public function testPublicPlaylistCanBeViewed(): void
    {
        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist]));

        $response->assertOk();
    }

    /**
     * By default, the Playlist Show Endpoint shall return a Playlist Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $playlist = Playlist::factory()
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistResource($playlist, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Show Endpoint shall return a Playlist Resource for soft deleted playlists.
     *
     * @return void
     */
    public function testSoftDelete(): void
    {
        $playlist = Playlist::factory()
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $playlist->delete();

        $playlist->unsetRelations();

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistResource($playlist, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
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

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->has(PlaylistTrack::factory(), Playlist::RELATION_FIRST)
            ->has(PlaylistTrack::factory(), Playlist::RELATION_LAST)
            ->has(PlaylistTrack::factory()->count($this->faker->randomDigitNotNull()), Playlist::RELATION_TRACKS)
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistResource($playlist, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
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

        $playlist = Playlist::factory()
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistResource($playlist, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Show Endpoint shall support constrained eager loading of images by facet.
     *
     * @return void
     */
    public function testImagesByFacet(): void
    {
        $facetFilter = ImageFacet::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Image::ATTRIBUTE_FACET => $facetFilter->description,
            ],
            IncludeParser::param() => Playlist::RELATION_IMAGES,
        ];

        $playlist = Playlist::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);

        $playlist->unsetRelations()->load([
            Playlist::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
            },
        ]);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistResource($playlist, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
