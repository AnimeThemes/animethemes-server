<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\AnimeImage;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\AnimeImage;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeImageDestroyTest.
 */
class AnimeImageDestroyTest extends TestCase
{
    use WithoutEvents;

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
     * The Anime Image Destroy Endpoint shall return an error if the anime image does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $anime = Anime::factory()->createOne();
        $image = Image::factory()->createOne();

        $user = User::factory()->withPermissions(['delete anime', 'delete image'])->createOne();

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

        $user = User::factory()->withPermissions(['delete anime', 'delete image'])->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animeimage.destroy', ['anime' => $animeImage->anime, 'image' => $animeImage->image]));

        $response->assertOk();
        static::assertModelMissing($animeImage);
    }
}
