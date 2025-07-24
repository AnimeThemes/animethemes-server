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

class ArtistImageStoreTest extends TestCase
{
    /**
     * The Artist Image Store Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $artist = Artist::factory()->createOne();
        $image = Image::factory()->createOne();

        $response = $this->post(route('api.artistimage.store', ['artist' => $artist, 'image' => $image]));

        $response->assertUnauthorized();
    }

    /**
     * The Artist Image Store Endpoint shall forbid users without the create artist & create image permissions.
     */
    public function testForbidden(): void
    {
        $artist = Artist::factory()->createOne();
        $image = Image::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistimage.store', ['artist' => $artist, 'image' => $image]));

        $response->assertForbidden();
    }

    /**
     * The Artist Image Store Endpoint shall create an artist image.
     */
    public function testCreate(): void
    {
        $artist = Artist::factory()->createOne();
        $image = Image::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE->format(Artist::class),
                CrudPermission::CREATE->format(Image::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.artistimage.store', ['artist' => $artist, 'image' => $image]));

        $response->assertCreated();
        static::assertDatabaseCount(ArtistImage::class, 1);
    }
}
