<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $image = Image::factory()->createOne();

    $response = delete(route('api.image.forceDelete', ['image' => $image]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $image = Image::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.image.forceDelete', ['image' => $image]));

    $response->assertForbidden();
});

test('deleted', function () {
    $image = Image::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Image::class))->createOne();

    Sanctum::actingAs($user);
    $response = delete(route('api.image.forceDelete', ['image' => $image]));

    $response->assertOk();
    $this->assertModelMissing($image);
});
