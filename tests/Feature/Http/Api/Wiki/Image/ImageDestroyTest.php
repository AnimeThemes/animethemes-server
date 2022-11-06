<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Image;

use App\Models\Auth\User;
use App\Models\Wiki\Image;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ImageDestroyTest.
 */
class ImageDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Image Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $image = Image::factory()->createOne();

        $response = $this->delete(route('api.image.destroy', ['image' => $image]));

        $response->assertUnauthorized();
    }

    /**
     * The Image Destroy Endpoint shall forbid users without the delete image permission.
     *
     * @return void
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
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $image = Image::factory()->createOne();

        $image->delete();

        $user = User::factory()->withPermission('delete image')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.image.destroy', ['image' => $image]));

        $response->assertNotFound();
    }

    /**
     * The Image Destroy Endpoint shall delete the image.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $image = Image::factory()->createOne();

        $user = User::factory()->withPermission('delete image')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.image.destroy', ['image' => $image]));

        $response->assertOk();
        static::assertSoftDeleted($image);
    }
}
