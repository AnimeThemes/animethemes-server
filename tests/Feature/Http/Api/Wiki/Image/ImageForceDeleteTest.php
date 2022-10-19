<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Image;

use App\Models\Auth\User;
use App\Models\Wiki\Image;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ImageForceDeleteTest.
 */
class ImageForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Image Force Delete Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $image = Image::factory()->createOne();

        $response = $this->delete(route('api.image.forceDelete', ['image' => $image]));

        $response->assertUnauthorized();
    }

    /**
     * The Image Force Delete Endpoint shall forbid users without the force delete image permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $image = Image::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.image.forceDelete', ['image' => $image]));

        $response->assertForbidden();
    }

    /**
     * The Image Force Delete Endpoint shall force delete the image.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $image = Image::factory()->createOne();

        $user = User::factory()->withPermission('force delete image')->createOne();

        Sanctum::actingAs($user);
        $response = $this->delete(route('api.image.forceDelete', ['image' => $image]));

        $response->assertOk();
        static::assertModelMissing($image);
    }
}
