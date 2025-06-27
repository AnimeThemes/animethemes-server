<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\ArtistImage;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\ArtistImage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ArtistImageDestroyTest.
 */
class ArtistImageDestroyTest extends TestCase
{
    /**
     * The Artist Image Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $artistImage = ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->delete(route('api.artistimage.destroy', ['artist' => $artistImage->artist, 'image' => $artistImage->image]));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Image Destroy Endpoint shall forbid users without the delete artist & delete image permissions.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $artistImage = ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artistimage.destroy', ['artist' => $artistImage->artist, 'image' => $artistImage->image]));

        $response->assertForbidden();
    }

    /**
     * The Artist Image Destroy Endpoint shall return an error if the artist image does not exist.
     *
     * @return void
     */
    public function test_not_found(): void
    {
        $artist = Artist::factory()->createOne();
        $image = Image::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Artist::class),
                CrudPermission::DELETE->format(Image::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artistimage.destroy', ['artist' => $artist, 'image' => $image]));

        $response->assertNotFound();
    }

    /**
     * The Artist Image Destroy Endpoint shall delete the artist image.
     *
     * @return void
     */
    public function test_deleted(): void
    {
        $artistImage = ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Artist::class),
                CrudPermission::DELETE->format(Image::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.artistimage.destroy', ['artist' => $artistImage->artist, 'image' => $artistImage->image]));

        $response->assertOk();
        static::assertModelMissing($artistImage);
    }
}
