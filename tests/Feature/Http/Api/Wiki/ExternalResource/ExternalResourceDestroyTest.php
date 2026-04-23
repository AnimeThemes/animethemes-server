<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function (): void {
    $resource = ExternalResource::factory()->createOne();

    $response = delete(route('api.resource.destroy', ['resource' => $resource]));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $resource = ExternalResource::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.resource.destroy', ['resource' => $resource]));

    $response->assertForbidden();
});

test('trashed', function (): void {
    $resource = ExternalResource::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(ExternalResource::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.resource.destroy', ['resource' => $resource]));

    $response->assertNotFound();
});

test('deleted', function (): void {
    $resource = ExternalResource::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(ExternalResource::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.resource.destroy', ['resource' => $resource]));

    $response->assertOk();
    $this->assertSoftDeleted($resource);
});
