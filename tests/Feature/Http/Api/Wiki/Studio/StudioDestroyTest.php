<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Studio;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $studio = Studio::factory()->createOne();

    $response = $this->delete(route('api.studio.destroy', ['studio' => $studio]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $studio = Studio::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.studio.destroy', ['studio' => $studio]));

    $response->assertForbidden();
});

test('trashed', function () {
    $studio = Studio::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Studio::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.studio.destroy', ['studio' => $studio]));

    $response->assertNotFound();
});

test('deleted', function () {
    $studio = Studio::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Studio::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.studio.destroy', ['studio' => $studio]));

    $response->assertOk();
    static::assertSoftDeleted($studio);
});
