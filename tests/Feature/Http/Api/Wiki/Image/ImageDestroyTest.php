<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $image = Image::factory()->createOne();

    $response = delete(route('api.image.destroy', ['image' => $image]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $image = Image::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.image.destroy', ['image' => $image]));

    $response->assertForbidden();
});

test('trashed', function () {
    $image = Image::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Image::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.image.destroy', ['image' => $image]));

    $response->assertNotFound();
});

test('deleted', function () {
    $image = Image::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Image::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.image.destroy', ['image' => $image]));

    $response->assertOk();
    $this->assertSoftDeleted($image);
});
