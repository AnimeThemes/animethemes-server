<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Image;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ImageRestoreTest.
 */
class ImageRestoreTest extends TestCase
{
    /**
     * The Image Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $image = Image::factory()->trashed()->createOne();

        $response = $this->patch(route('api.image.restore', ['image' => $image]));

        $response->assertUnauthorized();
    }

    /**
     * The Image Restore Endpoint shall forbid users without the restore image permission.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $image = Image::factory()->trashed()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.image.restore', ['image' => $image]));

        $response->assertForbidden();
    }

    /**
     * The Image Restore Endpoint shall forbid users from restoring an image that isn't trashed.
     *
     * @return void
     */
    public function test_trashed(): void
    {
        $image = Image::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Image::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.image.restore', ['image' => $image]));

        $response->assertForbidden();
    }

    /**
     * The Image Restore Endpoint shall restore the image.
     *
     * @return void
     */
    public function test_restored(): void
    {
        $image = Image::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Image::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.image.restore', ['image' => $image]));

        $response->assertOk();
        static::assertNotSoftDeleted($image);
    }
}
