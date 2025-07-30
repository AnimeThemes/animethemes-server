<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $resource = ExternalResource::factory()->createOne();

    $response = $this->delete(route('api.resource.forceDelete', ['resource' => $resource]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $resource = ExternalResource::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.resource.forceDelete', ['resource' => $resource]));

    $response->assertForbidden();
});

test('deleted', function () {
    $resource = ExternalResource::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(ExternalResource::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.resource.forceDelete', ['resource' => $resource]));

    $response->assertOk();
    static::assertModelMissing($resource);
});
