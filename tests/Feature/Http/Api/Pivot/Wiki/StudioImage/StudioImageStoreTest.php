<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\StudioImage;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioImage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class StudioImageStoreTest.
 */
class StudioImageStoreTest extends TestCase
{
    /**
     * The Studio Image Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $studio = Studio::factory()->createOne();
        $image = Image::factory()->createOne();

        $response = $this->post(route('api.studioimage.store', ['studio' => $studio, 'image' => $image]));

        $response->assertUnauthorized();
    }

    /**
     * The Studio Image Store Endpoint shall forbid users without the create studio & create image permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $studio = Studio::factory()->createOne();
        $image = Image::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.studioimage.store', ['studio' => $studio, 'image' => $image]));

        $response->assertForbidden();
    }

    /**
     * The Studio Image Store Endpoint shall create an studio image.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $studio = Studio::factory()->createOne();
        $image = Image::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(Studio::class),
                CrudPermission::CREATE()->format(Image::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.studioimage.store', ['studio' => $studio, 'image' => $image]));

        $response->assertCreated();
        static::assertDatabaseCount(StudioImage::class, 1);
    }
}
