<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ImageUpdateTest.
 */
class ImageUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Image Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $image = Image::factory()->createOne();

        $parameters = array_merge(
            Image::factory()->raw(),
            [Image::ATTRIBUTE_FACET => ImageFacet::getRandomInstance()->description],
        );

        $response = $this->put(route('api.image.update', ['image' => $image] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Image Update Endpoint shall update an image.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $image = Image::factory()->createOne();

        $parameters = array_merge(
            Image::factory()->raw(),
            [Image::ATTRIBUTE_FACET => ImageFacet::getRandomInstance()->description],
        );

        $user = User::factory()->createOne();

        $user->givePermissionTo('update image');

        Sanctum::actingAs($user);

        $response = $this->put(route('api.image.update', ['image' => $image] + $parameters));

        $response->assertOk();
    }
}
