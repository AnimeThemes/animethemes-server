<?php

declare(strict_types=1);

namespace Http\Api\Pivot\List\PlaylistImage;

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
use App\Http\Api\Schema\Pivot\List\PlaylistImageSchema;
use App\Http\Resources\Pivot\List\Resource\PlaylistImageResource;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Pivots\List\PlaylistImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistImageShowTest.
 */
class PlaylistImageShowTest extends TestCase
{
    use WithFaker;

    /**
     * The Playlist Image Show Endpoint shall return an error if the playlist image does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $playlist = Playlist::factory()
            ->for(User::factory())
            ->createOne([
                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
            ]);
        $image = Image::factory()->createOne();

        $response = $this->get(route('api.playlistimage.show', ['playlist' => $playlist, 'image' => $image]));

        $response->assertNotFound();
    }

    /**
     * The Playlist Image Show Endpoint shall forbid a private playlist image from being publicly viewed.
     *
     * @return void
     */
    public function testPrivatePlaylistImageCannotBePubliclyViewed(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $playlistImage = PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for(User::factory())
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE,
                    ])
            )
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Image Show Endpoint shall forbid the user from viewing a private playlist image if not owned.
     *
     * @return void
     */
    public function testPrivatePlaylistImageCannotBePubliclyIfNotOwned(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $playlistImage = PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for(User::factory())
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE,
                    ])
            )
            ->for(Image::factory())
            ->createOne();

        $user = User::factory()->withPermissions(CrudPermission::VIEW()->format(Playlist::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Image Show Endpoint shall allow a private playlist image to be viewed by the owner.
     *
     * @return void
     */
    public function testPrivatePlaylistImageCanBeViewedByOwner(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $user = User::factory()->withPermissions(CrudPermission::VIEW()->format(Playlist::class))->createOne();

        $playlistImage = PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for($user)
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE,
                    ])
            )
            ->for(Image::factory())
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

        $response->assertOk();
    }

    /**
     * The Playlist Image Show Endpoint shall allow an unlisted playlist image to be viewed.
     *
     * @return void
     */
    public function testUnlistedPlaylistImageCanBeViewed(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $playlistImage = PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for(User::factory())
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::UNLISTED,
                    ])
            )
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

        $response->assertOk();
    }

    /**
     * The Playlist Image Show Endpoint shall allow a public playlist image to be viewed.
     *
     * @return void
     */
    public function testPublicPlaylistCanBeViewed(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $playlistImage = PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for(User::factory())
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
                    ])
            )
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

        $response->assertOk();
    }

    /**
     * By default, the Playlist Image Show Endpoint shall return an Playlist Image Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $playlistImage = PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for(User::factory())
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
                    ])
            )
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

        $playlistImage->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistImageResource($playlistImage, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Image Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $schema = new PlaylistImageSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $playlistImage = PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for(User::factory())
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
                    ])
            )
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image] + $parameters));

        $playlistImage->unsetRelations()->load($includedPaths->all());

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistImageResource($playlistImage, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Image Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $schema = new PlaylistImageSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                PlaylistImageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $playlistImage = PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for(User::factory())
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
                    ])
            )
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image] + $parameters));

        $playlistImage->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistImageResource($playlistImage, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Image Show Endpoint shall support constrained eager loading of images by facet.
     *
     * @return void
     */
    public function testImagesByFacet(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        $facetFilter = ImageFacet::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Image::ATTRIBUTE_FACET => $facetFilter->description,
            ],
            IncludeParser::param() => PlaylistImage::RELATION_IMAGE,
        ];

        $playlistImage = PlaylistImage::factory()
            ->for(
                Playlist::factory()
                    ->for(User::factory())
                    ->state([
                        Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
                    ])
            )
            ->for(Image::factory())
            ->createOne();

        $response = $this->get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image] + $parameters));

        $playlistImage->unsetRelations()->load([
            PlaylistImage::RELATION_IMAGE => function (BelongsTo $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistImageResource($playlistImage, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
