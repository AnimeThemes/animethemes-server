<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Studio;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function () {
    $studio = Studio::factory()->trashed()->createOne();

    $response = patch(route('api.studio.restore', ['studio' => $studio]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $studio = Studio::factory()->trashed()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.studio.restore', ['studio' => $studio]));

    $response->assertForbidden();
});

test('trashed', function () {
    $studio = Studio::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Studio::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.studio.restore', ['studio' => $studio]));

    $response->assertForbidden();
});

test('restored', function () {
    $studio = Studio::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Studio::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.studio.restore', ['studio' => $studio]));

    $response->assertOk();
    $this->assertNotSoftDeleted($studio);
});
