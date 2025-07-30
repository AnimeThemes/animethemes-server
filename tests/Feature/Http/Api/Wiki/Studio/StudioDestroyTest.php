<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Studio;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $studio = Studio::factory()->createOne();

    $response = delete(route('api.studio.destroy', ['studio' => $studio]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $studio = Studio::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.studio.destroy', ['studio' => $studio]));

    $response->assertForbidden();
});

test('trashed', function () {
    $studio = Studio::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Studio::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.studio.destroy', ['studio' => $studio]));

    $response->assertNotFound();
});

test('deleted', function () {
    $studio = Studio::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Studio::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.studio.destroy', ['studio' => $studio]));

    $response->assertOk();
    $this->assertSoftDeleted($studio);
});
