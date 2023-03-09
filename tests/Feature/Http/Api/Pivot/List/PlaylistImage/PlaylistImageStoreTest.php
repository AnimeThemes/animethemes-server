<?php

declare(strict_types=1);

namespace Http\Api\Pivot\List\PlaylistImage;

use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Pivots\List\PlaylistImage;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistImageStoreTest.
 */
class PlaylistImageStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Playlist Image Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $playlistImage = PlaylistImage::factory()
            ->for(Playlist::factory())
            ->for(Image::factory())
            ->makeOne();

        $response = $this->post(route('api.playlistimage.store', $playlistImage->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Playlist Image Store Endpoint shall forbid users without the create playlist & create image permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $playlistImage = PlaylistImage::factory()
            ->for(Playlist::factory())
            ->for(Image::factory())
            ->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlistimage.store', $playlistImage->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Playlist Image Store Endpoint shall require playlist and image fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(['create playlist', 'create image'])->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlistimage.store'));

        $response->assertJsonValidationErrors([
            PlaylistImage::ATTRIBUTE_PLAYLIST,
            PlaylistImage::ATTRIBUTE_IMAGE,
        ]);
    }

    /**
     * The Playlist Image Store Endpoint shall create an playlist image.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = [
            PlaylistImage::ATTRIBUTE_PLAYLIST => Playlist::factory()->createOne()->getKey(),
            PlaylistImage::ATTRIBUTE_IMAGE => Image::factory()->createOne()->getKey(),
        ];

        $user = User::factory()->withPermissions(['create playlist', 'create image'])->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlistimage.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(PlaylistImage::TABLE, 1);
    }
}
