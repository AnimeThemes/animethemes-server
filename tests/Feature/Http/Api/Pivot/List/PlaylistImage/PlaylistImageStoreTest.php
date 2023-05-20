<?php

declare(strict_types=1);

namespace Http\Api\Pivot\List\PlaylistImage;

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
 * Class PlaylistImageStoreTest.
 */
class PlaylistImageStoreTest extends TestCase
{
    use WithFaker;

    /**
     * The Playlist Image Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $playlist = Playlist::factory()->createOne();
        $image = Image::factory()->createOne();

        $response = $this->post(route('api.playlistimage.store', ['playlist' => $playlist, 'image' => $image]));

        $response->assertUnauthorized();
    }

    /**
     * The Playlist Image Store Endpoint shall forbid users without the create playlist & create image permissions.
     *
     * @return void
     */
    public function testForbiddenIfMissingPermission(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $playlist = Playlist::factory()->createOne();
        $image = Image::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlistimage.store', ['playlist' => $playlist, 'image' => $image]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Image Store Endpoint shall forbid users from creating playlist images
     * if the Allow Playlist Management feature is inactive.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::deactivate(AllowPlaylistManagement::class);

        $playlist = Playlist::factory()->createOne();
        $image = Image::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(Playlist::class),
                CrudPermission::CREATE()->format(Image::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlistimage.store', ['playlist' => $playlist, 'image' => $image]));

        $response->assertForbidden();
    }

    /**
     * The Playlist Image Store Endpoint shall create an playlist image.
     *
     * @return void
     */
    public function testCreate(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class);

        $playlist = Playlist::factory()->createOne();
        $image = Image::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(Playlist::class),
                CrudPermission::CREATE()->format(Image::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlistimage.store', ['playlist' => $playlist, 'image' => $image]));

        $response->assertCreated();
        static::assertDatabaseCount(PlaylistImage::class, 1);
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to create playlist images
     * even if the Allow Playlist Management feature is inactive.
     *
     * @return void
     */
    public function testCreatePermittedForBypass(): void
    {
        Event::fakeExcept(PlaylistCreated::class);

        Feature::activate(AllowPlaylistManagement::class, $this->faker->boolean());

        $playlist = Playlist::factory()->createOne();
        $image = Image::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(Playlist::class),
                CrudPermission::CREATE()->format(Image::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlistimage.store', ['playlist' => $playlist, 'image' => $image]));

        $response->assertCreated();
    }
}
