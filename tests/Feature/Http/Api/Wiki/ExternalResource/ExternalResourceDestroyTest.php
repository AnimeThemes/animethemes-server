<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $resource = ExternalResource::factory()->createOne();

    $response = $this->delete(route('api.resource.destroy', ['resource' => $resource]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $resource = ExternalResource::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.resource.destroy', ['resource' => $resource]));

    $response->assertForbidden();
});

test('trashed', function () {
    $resource = ExternalResource::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(ExternalResource::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.resource.destroy', ['resource' => $resource]));

    $response->assertNotFound();
});

test('deleted', function () {
    $resource = ExternalResource::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(ExternalResource::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.resource.destroy', ['resource' => $resource]));

    $response->assertOk();
    static::assertSoftDeleted($resource);
});
