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
 * Class AnimeImageStoreTest.
 */
class AnimeImageStoreTest extends TestCase
{
    /**
     * The Anime Image Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $anime = Anime::factory()->createOne();
        $image = Image::factory()->createOne();

        $response = $this->post(route('api.animeimage.store', ['anime' => $anime, 'image' => $image]));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Image Store Endpoint shall forbid users without the create anime & create image permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $anime = Anime::factory()->createOne();
        $image = Image::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animeimage.store', ['anime' => $anime, 'image' => $image]));

        $response->assertForbidden();
    }

    /**
     * The Anime Image Store Endpoint shall create an anime image.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $anime = Anime::factory()->createOne();
        $image = Image::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(Anime::class),
                CrudPermission::CREATE()->format(Image::class))
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animeimage.store', ['anime' => $anime, 'image' => $image]));

        $response->assertCreated();
        static::assertDatabaseCount(AnimeImage::class, 1);
    }
}
