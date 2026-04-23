<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Studio;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function (): void {
    $studio = Studio::factory()->makeOne();

    $response = post(route('api.studio.store', $studio->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $studio = Studio::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.studio.store', $studio->toArray()));

    $response->assertForbidden();
});

test('required fields', function (): void {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Studio::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.studio.store'));

    $response->assertJsonValidationErrors([
        Studio::ATTRIBUTE_NAME,
        Studio::ATTRIBUTE_SLUG,
    ]);
});

test('create', function (): void {
    $parameters = Studio::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Studio::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.studio.store', $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(Studio::class, 1);
});
