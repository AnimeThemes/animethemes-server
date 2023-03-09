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
 * Class PlaylistImageDestroyTest.
 */
class PlaylistImageDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Playlist Image Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $playlistImage = PlaylistImage::factory()
            ->for(Playlist::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->delete(route('api.playlistimage.destroy', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

        $response->assertUnauthorized();
    }

    /**
     * The Playlist Image Destroy Endpoint shall forbid users without the delete playlist & delete image permissions.
     *
     * @return void
     */
    public function testForbiddenIfMissingPermission(): void
    {
        $playlistImage = PlaylistImage::factory()
            ->for(Playlist::factory())
            ->for(Image::factory())
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlistimage.destroy', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Image Destroy Endpoint shall forbid users from deleting the playlist image if they don't own the playlist.
     *
     * @return void
     */
    public function testForbiddenIfNotOwnPlaylist(): void
    {
        $playlistImage = PlaylistImage::factory()
            ->for(Playlist::factory()->for(User::factory()))
            ->for(Image::factory())
            ->createOne();

        $user = User::factory()->withPermissions(['delete playlist', 'delete image'])->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlistimage.destroy', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Image Destroy Endpoint shall return an error if the playlist image does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $user = User::factory()->withPermissions(['delete playlist', 'delete image'])->createOne();

        $playlist = Playlist::factory()
            ->for($user)
            ->createOne();
        $image = Image::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlistimage.destroy', ['playlist' => $playlist, 'image' => $image]));

        $response->assertNotFound();
    }

    /**
     * The Playlist Image Destroy Endpoint shall delete the playlist image.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $user = User::factory()->withPermissions(['delete playlist', 'delete image'])->createOne();

        $playlistImage = PlaylistImage::factory()
            ->for(Playlist::factory()->for($user))
            ->for(Image::factory())
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlistimage.destroy', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

        $response->assertOk();
        static::assertModelMissing($playlistImage);
    }
}
