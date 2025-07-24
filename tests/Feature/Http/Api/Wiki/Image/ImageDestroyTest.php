<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Image;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ImageDestroyTest extends TestCase
{
    /**
     * The Image Destroy Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $image = Image::factory()->createOne();

        $response = $this->delete(route('api.image.destroy', ['image' => $image]));

        $response->assertUnauthorized();
    }

    /**
     * The Image Destroy Endpoint shall forbid users without the delete image permission.
     */
    public function testForbidden(): void
    {
        $image = Image::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.image.destroy', ['image' => $image]));

        $response->assertForbidden();
    }

    /**
     * The Image Destroy Endpoint shall forbid users from deleting an image that is trashed.
     */
    public function testTrashed(): void
    {
        $image = Image::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Image::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.image.destroy', ['image' => $image]));

        $response->assertNotFound();
    }

    /**
     * The Image Destroy Endpoint shall delete the image.
     */
    public function testDeleted(): void
    {
        $image = Image::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Image::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.image.destroy', ['image' => $image]));

        $response->assertOk();
        static::assertSoftDeleted($image);
    }
}
