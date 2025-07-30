<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $resource = ExternalResource::factory()->trashed()->createOne();

    $response = $this->patch(route('api.resource.restore', ['resource' => $resource]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $resource = ExternalResource::factory()->trashed()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->patch(route('api.resource.restore', ['resource' => $resource]));

    $response->assertForbidden();
});

test('trashed', function () {
    $resource = ExternalResource::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(ExternalResource::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->patch(route('api.resource.restore', ['resource' => $resource]));

    $response->assertForbidden();
});

test('restored', function () {
    $resource = ExternalResource::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(ExternalResource::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->patch(route('api.resource.restore', ['resource' => $resource]));

    $response->assertOk();
    static::assertNotSoftDeleted($resource);
});
