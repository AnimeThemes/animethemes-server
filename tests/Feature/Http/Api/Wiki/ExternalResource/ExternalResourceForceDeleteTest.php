<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function (): void {
    $resource = ExternalResource::factory()->createOne();

    $response = delete(route('api.resource.forceDelete', ['resource' => $resource]));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $resource = ExternalResource::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.resource.forceDelete', ['resource' => $resource]));

    $response->assertForbidden();
});

test('deleted', function (): void {
    $resource = ExternalResource::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(ExternalResource::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.resource.forceDelete', ['resource' => $resource]));

    $response->assertOk();
    $this->assertModelMissing($resource);
});
