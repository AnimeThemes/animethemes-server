<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $studioResource = StudioResource::factory()
        ->for(Studio::factory())
        ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
        ->createOne();

    $response = $this->delete(route('api.studioresource.destroy', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $studioResource = StudioResource::factory()
        ->for(Studio::factory())
        ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.studioresource.destroy', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource]));

    $response->assertForbidden();
});

test('not found', function () {
    $studio = Studio::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Studio::class),
            CrudPermission::DELETE->format(ExternalResource::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.studioresource.destroy', ['studio' => $studio, 'resource' => $resource]));

    $response->assertNotFound();
});

test('deleted', function () {
    $studioResource = StudioResource::factory()
        ->for(Studio::factory())
        ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
        ->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Studio::class),
            CrudPermission::DELETE->format(ExternalResource::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.studioresource.destroy', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource]));

    $response->assertOk();
    static::assertModelMissing($studioResource);
});
