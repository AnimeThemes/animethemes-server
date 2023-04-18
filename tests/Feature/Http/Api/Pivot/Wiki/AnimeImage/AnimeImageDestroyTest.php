<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\AnimeImage;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\AnimeImage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeImageDestroyTest.
 */
class AnimeImageDestroyTest extends TestCase
{
    /**
     * The Anime Image Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $animeImage = AnimeImage::factory()
            ->for(Anime::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->delete(route('api.animeimage.destroy', ['anime' => $animeImage->anime, 'image' => $animeImage->image]));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Image Destroy Endpoint shall forbid users without the delete anime & delete image permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $animeImage = AnimeImage::factory()
            ->for(Anime::factory())
            ->for(Image::factory())
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animeimage.destroy', ['anime' => $animeImage->anime, 'image' => $animeImage->image]));

        $response->assertForbidden();
    }

    /**
     * The Anime Image Destroy Endpoint shall return an error if the anime image does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $anime = Anime::factory()->createOne();
        $image = Image::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE()->format(Anime::class),
                CrudPermission::DELETE()->format(Image::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animeimage.destroy', ['anime' => $anime, 'image' => $image]));

        $response->assertNotFound();
    }

    /**
     * The Anime Image Destroy Endpoint shall delete the anime image.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $animeImage = AnimeImage::factory()
            ->for(Anime::factory())
            ->for(Image::factory())
            ->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE()->format(Anime::class),
                CrudPermission::DELETE()->format(Image::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animeimage.destroy', ['anime' => $animeImage->anime, 'image' => $animeImage->image]));

        $response->assertOk();
        static::assertModelMissing($animeImage);
    }
}
