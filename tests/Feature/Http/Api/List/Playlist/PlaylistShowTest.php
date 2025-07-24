<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\Playlist;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Models\Wiki\ImageFacet;
use App\Events\List\Playlist\PlaylistCreated;
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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PlaylistShowTest extends TestCase
{
    use WithFaker;

    /**
     * The Playlist Show Endpoint shall forbid a private playlist from being publicly viewed.
     */
    public function testPrivatePlaylistCannotBePubliclyViewed(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
            ]);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Show Endpoint shall forbid the user from viewing a private playlist if not owned.
     */
    public function testPrivatePlaylistCannotBePubliclyIfNotOwned(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
            ]);

        $user = User::factory()->withPermissions(CrudPermission::VIEW->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Show Endpoint shall allow a private playlist to be viewed by the owner.
     */
    public function testPrivatePlaylistCanBeViewedByOwner(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $user = User::factory()->withPermissions(CrudPermission::VIEW->format(Playlist::class))->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
            ]);

        Sanctum::actingAs($user);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist]));

        $response->assertOk();
    }

    /**
     * The Playlist Show Endpoint shall allow an unlisted playlist to be viewed.
     */
    public function testUnlistedPlaylistCanBeViewed(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::UNLISTED->value,
            ]);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist]));

        $response->assertOk();
    }

    /**
     * The Playlist Show Endpoint shall allow a public playlist to be viewed.
     */
    public function testPublicPlaylistCanBeViewed(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
            ]);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist]));

        $response->assertOk();
    }

    /**
     * By default, the Playlist Show Endpoint shall return a Playlist Resource.
     */
    public function testDefault(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $playlist = Playlist::factory()
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
            ]);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist]));

        $response->assertJson(
            json_decode(
                json_encode(
                    new PlaylistResource($playlist, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Show Endpoint shall allow inclusion of related resources.
     */
    public function testAllowedIncludePaths(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

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
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
            ]);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new PlaylistResource($playlist, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Show Endpoint shall implement sparse fieldsets.
     */
    public function testSparseFieldsets(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

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
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
            ]);

        $response = $this->get(route('api.playlist.show', ['playlist' => $playlist] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new PlaylistResource($playlist, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Show Endpoint shall support constrained eager loading of images by facet.
     */
    public function testImagesByFacet(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $facetFilter = Arr::random(ImageFacet::cases());

        $parameters = [
            FilterParser::param() => [
                Image::ATTRIBUTE_FACET => $facetFilter->localize(),
            ],
            IncludeParser::param() => Playlist::RELATION_IMAGES,
        ];

        $playlist = Playlist::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
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
                    new PlaylistResource($playlist, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
