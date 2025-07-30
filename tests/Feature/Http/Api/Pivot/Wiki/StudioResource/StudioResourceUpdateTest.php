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

    $parameters = StudioResource::factory()->raw();

    $response = $this->put(route('api.studioresource.update', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $studioResource = StudioResource::factory()
        ->for(Studio::factory())
        ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
        ->createOne();

    $parameters = StudioResource::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.studioresource.update', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $studioResource = StudioResource::factory()
        ->for(Studio::factory())
        ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
        ->createOne();

    $parameters = StudioResource::factory()->raw();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::UPDATE->format(Studio::class),
            CrudPermission::UPDATE->format(ExternalResource::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.studioresource.update', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource] + $parameters));

    $response->assertOk();
});
