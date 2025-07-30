<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioImage;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $studio = Studio::factory()->createOne();
    $image = Image::factory()->createOne();

    $response = post(route('api.studioimage.store', ['studio' => $studio, 'image' => $image]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $studio = Studio::factory()->createOne();
    $image = Image::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.studioimage.store', ['studio' => $studio, 'image' => $image]));

    $response->assertForbidden();
});

test('create', function () {
    $studio = Studio::factory()->createOne();
    $image = Image::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(Studio::class),
            CrudPermission::CREATE->format(Image::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.studioimage.store', ['studio' => $studio, 'image' => $image]));

    $response->assertCreated();
    $this->assertDatabaseCount(StudioImage::class, 1);
});
