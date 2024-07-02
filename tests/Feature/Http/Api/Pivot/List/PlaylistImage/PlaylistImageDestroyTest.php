<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\List\PlaylistImage;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Events\List\Playlist\PlaylistCreated;
use App\Features\AllowPlaylistManagement;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Pivots\List\PlaylistImage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistImageDestroyTest.
 */
class PlaylistImageDestroyTest extends TestCase
{
    use WithFaker;

    /**
     * The Playlist Image Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

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
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

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
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $playlistImage = PlaylistImage::factory()
            ->for(Playlist::factory()->for(User::factory()))
            ->for(Image::factory())
            ->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Playlist::class),
                CrudPermission::DELETE->format(Image::class))
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlistimage.destroy', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Image Destroy Endpoint shall forbid users from destroying playlist images
     * if the Allow Playlist Management feature is inactive.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::deactivate(AllowPlaylistManagement::class);

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Playlist::class),
                CrudPermission::DELETE->format(Image::class))
            ->createOne();

        $playlistImage = PlaylistImage::factory()
            ->for(Playlist::factory()->for($user))
            ->for(Image::factory())
            ->createOne();

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
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Playlist::class),
                CrudPermission::DELETE->format(Image::class)
            )
            ->createOne();

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
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Playlist::class),
                CrudPermission::DELETE->format(Image::class)
            )
            ->createOne();

        $playlistImage = PlaylistImage::factory()
            ->for(Playlist::factory()->for($user))
            ->for(Image::factory())
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlistimage.destroy', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

        $response->assertOk();
        static::assertModelMissing($playlistImage);
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to destroy playlist images
     * even if the Allow Playlist Management feature is inactive.
     *
     * @return void
     */
    public function testDestroyPermittedForBypass(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Playlist::class),
                CrudPermission::DELETE->format(Image::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS->value
            )
            ->createOne();

        $playlistImage = PlaylistImage::factory()
            ->for(Playlist::factory()->for($user))
            ->for(Image::factory())
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.playlistimage.destroy', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

        $response->assertOk();
    }
}
