<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\StudioImage;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioImage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudioImageDestroyTest extends TestCase
{
    /**
     * The Studio Image Destroy Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $studioImage = StudioImage::factory()
            ->for(Studio::factory())
            ->for(Image::factory())
            ->createOne();

        $response = $this->delete(route('api.studioimage.destroy', ['studio' => $studioImage->studio, 'image' => $studioImage->image]));

        $response->assertUnauthorized();
    }

    /**
     * The Studio Image Destroy Endpoint shall forbid users without the delete studio & delete image permissions.
     */
    public function testForbidden(): void
    {
        $studioImage = StudioImage::factory()
            ->for(Studio::factory())
            ->for(Image::factory())
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.studioimage.destroy', ['studio' => $studioImage->studio, 'image' => $studioImage->image]));

        $response->assertForbidden();
    }

    /**
     * The Studio Image Destroy Endpoint shall return an error if the studio image does not exist.
     */
    public function testNotFound(): void
    {
        $studio = Studio::factory()->createOne();
        $image = Image::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Studio::class),
                CrudPermission::DELETE->format(Image::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.studioimage.destroy', ['studio' => $studio, 'image' => $image]));

        $response->assertNotFound();
    }

    /**
     * The Studio Image Destroy Endpoint shall delete the studio image.
     */
    public function testDeleted(): void
    {
        $studioImage = StudioImage::factory()
            ->for(Studio::factory())
            ->for(Image::factory())
            ->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE->format(Studio::class),
                CrudPermission::DELETE->format(Image::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.studioimage.destroy', ['studio' => $studioImage->studio, 'image' => $studioImage->image]));

        $response->assertOk();
        static::assertModelMissing($studioImage);
    }
}
