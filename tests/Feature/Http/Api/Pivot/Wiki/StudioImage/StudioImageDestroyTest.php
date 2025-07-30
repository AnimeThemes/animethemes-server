<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioImage;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $studioImage = StudioImage::factory()
        ->for(Studio::factory())
        ->for(Image::factory())
        ->createOne();

    $response = delete(route('api.studioimage.destroy', ['studio' => $studioImage->studio, 'image' => $studioImage->image]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $studioImage = StudioImage::factory()
        ->for(Studio::factory())
        ->for(Image::factory())
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.studioimage.destroy', ['studio' => $studioImage->studio, 'image' => $studioImage->image]));

    $response->assertForbidden();
});

test('not found', function () {
    $studio = Studio::factory()->createOne();
    $image = Image::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Studio::class),
            CrudPermission::DELETE->format(Image::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.studioimage.destroy', ['studio' => $studio, 'image' => $image]));

    $response->assertNotFound();
});

test('deleted', function () {
    $studioImage = StudioImage::factory()
        ->for(Studio::factory())
        ->for(Image::factory())
        ->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Studio::class),
            CrudPermission::DELETE->format(Image::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.studioimage.destroy', ['studio' => $studioImage->studio, 'image' => $studioImage->image]));

    $response->assertOk();
    $this->assertModelMissing($studioImage);
});
