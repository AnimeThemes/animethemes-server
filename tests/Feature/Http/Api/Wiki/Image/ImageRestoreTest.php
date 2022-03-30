<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Image;

use App\Models\Auth\User;
use App\Models\Wiki\Image;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ImageRestoreTest.
 */
class ImageRestoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Image Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $image = Image::factory()->createOne();

        $image->delete();

        $response = $this->patch(route('api.image.restore', ['image' => $image]));

        $response->assertForbidden();
    }

    /**
     * The Image Restore Endpoint shall restore the image.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $image = Image::factory()->createOne();

        $image->delete();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['image:restore']
        );

        $response = $this->patch(route('api.image.restore', ['image' => $image]));

        $response->assertOk();
        static::assertNotSoftDeleted($image);
    }
}
