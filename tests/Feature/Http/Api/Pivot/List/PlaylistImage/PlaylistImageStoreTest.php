<?php

declare(strict_types=1);

namespace Http\Api\Pivot\List\PlaylistImage;

use App\Constants\Config\FlagConstants;
use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Pivots\List\PlaylistImage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PlaylistImageStoreTest.
 */
class PlaylistImageStoreTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Playlist Image Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

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
    public function testForbiddenIfMissingPermission(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

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
     * The Playlist Image Store Endpoint shall forbid users from creating playlist images
     * if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, false);

        $parameters = [
            PlaylistImage::ATTRIBUTE_PLAYLIST => Playlist::factory()->createOne()->getKey(),
            PlaylistImage::ATTRIBUTE_IMAGE => Image::factory()->createOne()->getKey(),
        ];

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(Playlist::class),
                CrudPermission::CREATE()->format(Image::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlistimage.store', $parameters));

        $response->assertForbidden();
    }

    /**
     * The Playlist Image Store Endpoint shall require playlist and image fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(Playlist::class),
                CrudPermission::CREATE()->format(Image::class)
            )
            ->createOne();

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
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, true);

        $parameters = [
            PlaylistImage::ATTRIBUTE_PLAYLIST => Playlist::factory()->createOne()->getKey(),
            PlaylistImage::ATTRIBUTE_IMAGE => Image::factory()->createOne()->getKey(),
        ];

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(Playlist::class),
                CrudPermission::CREATE()->format(Image::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlistimage.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(PlaylistImage::TABLE, 1);
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to create playlist images
     * even if the 'flags.allow_playlist_management' property is disabled.
     *
     * @return void
     */
    public function testCreatePermittedForBypass(): void
    {
        Config::set(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED, $this->faker->boolean());

        $parameters = [
            PlaylistImage::ATTRIBUTE_PLAYLIST => Playlist::factory()->createOne()->getKey(),
            PlaylistImage::ATTRIBUTE_IMAGE => Image::factory()->createOne()->getKey(),
        ];

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(Playlist::class),
                CrudPermission::CREATE()->format(Image::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.playlistimage.store', $parameters));

        $response->assertCreated();
    }
}
