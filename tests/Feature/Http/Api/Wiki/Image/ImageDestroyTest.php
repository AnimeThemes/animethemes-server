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

        $response->assertForbidden();
    }

    /**
     * The Image Destroy Endpoint shall delete the image.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $image = Image::factory()->createOne();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['image:delete']
        );

        $response = $this->delete(route('api.image.destroy', ['image' => $image]));

        $response->assertOk();
        static::assertSoftDeleted($image);
    }
}
