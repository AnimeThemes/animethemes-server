<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Studio;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

test('protected', function () {
    $studio = Studio::factory()->createOne();

    $parameters = Studio::factory()->raw();

    $response = put(route('api.studio.update', ['studio' => $studio] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $studio = Studio::factory()->createOne();

    $parameters = Studio::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.studio.update', ['studio' => $studio] + $parameters));

    $response->assertForbidden();
});

test('trashed', function () {
    $studio = Studio::factory()->trashed()->createOne();

    $parameters = Studio::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Studio::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.studio.update', ['studio' => $studio] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $studio = Studio::factory()->createOne();

    $parameters = Studio::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Studio::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.studio.update', ['studio' => $studio] + $parameters));

    $response->assertOk();
});
