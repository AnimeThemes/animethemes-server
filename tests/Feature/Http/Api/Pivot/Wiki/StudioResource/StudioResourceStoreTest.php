<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $studio = Studio::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $parameters = StudioResource::factory()->raw();

    $response = $this->post(route('api.studioresource.store', ['studio' => $studio, 'resource' => $resource] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $studio = Studio::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $parameters = StudioResource::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.studioresource.store', ['studio' => $studio, 'resource' => $resource] + $parameters));

    $response->assertForbidden();
});

test('create', function () {
    $studio = Studio::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $parameters = StudioResource::factory()->raw();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(Studio::class),
            CrudPermission::CREATE->format(ExternalResource::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.studioresource.store', ['studio' => $studio, 'resource' => $resource] + $parameters));

    $response->assertCreated();
    static::assertDatabaseCount(StudioResource::class, 1);
});
