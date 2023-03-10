<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\ArtistImage;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\ArtistImage;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ArtistImageStoreTest.
 */
class ArtistImageStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Artist Image Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $artistImage = ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->makeOne();

        $response = $this->post(route('api.artistimage.store', $artistImage->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Image Store Endpoint shall forbid users without the create artist & create image permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $artistImage = ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistimage.store', $artistImage->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Artist Image Store Endpoint shall require artist and image fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions([CrudPermission::CREATE()->format(Artist::class), CrudPermission::CREATE()->format(Image::class)])->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistimage.store'));

        $response->assertJsonValidationErrors([
            ArtistImage::ATTRIBUTE_ARTIST,
            ArtistImage::ATTRIBUTE_IMAGE,
        ]);
    }

    /**
     * The Artist Image Store Endpoint shall create an artist image.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = [
            ArtistImage::ATTRIBUTE_ARTIST => Artist::factory()->createOne()->getKey(),
            ArtistImage::ATTRIBUTE_IMAGE => Image::factory()->createOne()->getKey(),
        ];

        $user = User::factory()->withPermissions([CrudPermission::CREATE()->format(Artist::class), CrudPermission::CREATE()->format(Image::class)])->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistimage.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(ArtistImage::TABLE, 1);
    }
}
