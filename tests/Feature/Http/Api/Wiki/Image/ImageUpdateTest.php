<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Image;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ImageUpdateTest.
 */
class ImageUpdateTest extends TestCase
{
    /**
     * The Image Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $image = Image::factory()->createOne();

        $facet = Arr::random(ImageFacet::cases());

        $parameters = array_merge(
            Image::factory()->raw(),
            [Image::ATTRIBUTE_FACET => $facet->localize()],
        );

        $response = $this->put(route('api.image.update', ['image' => $image] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Image Update Endpoint shall forbid users without the update image permission.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $image = Image::factory()->createOne();

        $facet = Arr::random(ImageFacet::cases());

        $parameters = array_merge(
            Image::factory()->raw(),
            [Image::ATTRIBUTE_FACET => $facet->localize()],
        );

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.image.update', ['image' => $image] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Image Update Endpoint shall forbid users from updating an image that is trashed.
     *
     * @return void
     */
    public function test_trashed(): void
    {
        $image = Image::factory()->trashed()->createOne();

        $facet = Arr::random(ImageFacet::cases());

        $parameters = array_merge(
            Image::factory()->raw(),
            [Image::ATTRIBUTE_FACET => $facet->localize()],
        );

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Image::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.image.update', ['image' => $image] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Image Update Endpoint shall update an image.
     *
     * @return void
     */
    public function test_update(): void
    {
        $image = Image::factory()->createOne();

        $facet = Arr::random(ImageFacet::cases());

        $parameters = array_merge(
            Image::factory()->raw(),
            [Image::ATTRIBUTE_FACET => $facet->localize()],
        );

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Image::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.image.update', ['image' => $image] + $parameters));

        $response->assertOk();
    }
}
