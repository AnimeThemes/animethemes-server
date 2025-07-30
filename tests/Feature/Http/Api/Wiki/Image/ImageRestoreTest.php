<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function () {
    $image = Image::factory()->trashed()->createOne();

    $response = patch(route('api.image.restore', ['image' => $image]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $image = Image::factory()->trashed()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.image.restore', ['image' => $image]));

    $response->assertForbidden();
});

test('trashed', function () {
    $image = Image::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Image::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.image.restore', ['image' => $image]));

    $response->assertForbidden();
});

test('restored', function () {
    $image = Image::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Image::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.image.restore', ['image' => $image]));

    $response->assertOk();
    $this->assertNotSoftDeleted($image);
});
