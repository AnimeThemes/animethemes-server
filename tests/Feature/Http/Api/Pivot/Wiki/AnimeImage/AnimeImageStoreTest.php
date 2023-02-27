<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\AnimeImage;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\AnimeImage;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeImageStoreTest.
 */
class AnimeImageStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Anime Image Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $animeImage = AnimeImage::factory()
            ->for(Anime::factory())
            ->for(Image::factory())
            ->makeOne();

        $response = $this->post(route('api.animeimage.store', $animeImage->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Image Store Endpoint shall forbid users without the create anime & create image permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $animeImage = AnimeImage::factory()
            ->for(Anime::factory())
            ->for(Image::factory())
            ->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animeimage.store', $animeImage->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Anime Image Store Endpoint shall require anime and image fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(['create anime', 'create image'])->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animeimage.store'));

        $response->assertJsonValidationErrors([
            AnimeImage::ATTRIBUTE_ANIME,
            AnimeImage::ATTRIBUTE_IMAGE,
        ]);
    }

    /**
     * The Anime Image Store Endpoint shall create an anime image.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = [
            AnimeImage::ATTRIBUTE_ANIME => Anime::factory()->createOne()->getKey(),
            AnimeImage::ATTRIBUTE_IMAGE => Image::factory()->createOne()->getKey(),
        ];

        $user = User::factory()->withPermissions(['create anime', 'create image'])->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animeimage.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(AnimeImage::TABLE, 1);
    }
}
