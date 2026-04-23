<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function (): void {
    $resource = ExternalResource::factory()->trashed()->createOne();

    $response = patch(route('api.resource.restore', ['resource' => $resource]));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $resource = ExternalResource::factory()->trashed()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.resource.restore', ['resource' => $resource]));

    $response->assertForbidden();
});

test('trashed', function (): void {
    $resource = ExternalResource::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(ExternalResource::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.resource.restore', ['resource' => $resource]));

    $response->assertOk();
});

test('restored', function (): void {
    $resource = ExternalResource::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(ExternalResource::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.resource.restore', ['resource' => $resource]));

    $response->assertOk();
    $this->assertNotSoftDeleted($resource);
});
