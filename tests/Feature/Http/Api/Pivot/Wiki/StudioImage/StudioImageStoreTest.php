<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\StudioImage;

use App\Models\Auth\User;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioImage;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class StudioImageStoreTest.
 */
class StudioImageStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Studio Image Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $studioImage = StudioImage::factory()
            ->for(Studio::factory())
            ->for(Image::factory())
            ->makeOne();

        $response = $this->post(route('api.studioimage.store', $studioImage->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Studio Image Store Endpoint shall forbid users without the create studio & create image permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $studioImage = StudioImage::factory()
            ->for(Studio::factory())
            ->for(Image::factory())
            ->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.studioimage.store', $studioImage->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Studio Image Store Endpoint shall require studio and image fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(['create studio', 'create image'])->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.studioimage.store'));

        $response->assertJsonValidationErrors([
            StudioImage::ATTRIBUTE_STUDIO,
            StudioImage::ATTRIBUTE_IMAGE,
        ]);
    }

    /**
     * The Studio Image Store Endpoint shall create an studio image.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = [
            StudioImage::ATTRIBUTE_STUDIO => Studio::factory()->createOne()->getKey(),
            StudioImage::ATTRIBUTE_IMAGE => Image::factory()->createOne()->getKey(),
        ];

        $user = User::factory()->withPermissions(['create studio', 'create image'])->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.studioimage.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(StudioImage::TABLE, 1);
    }
}
