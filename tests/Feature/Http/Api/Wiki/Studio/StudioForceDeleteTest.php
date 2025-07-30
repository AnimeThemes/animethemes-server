<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Studio;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $studio = Studio::factory()->createOne();

    $response = $this->delete(route('api.studio.forceDelete', ['studio' => $studio]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $studio = Studio::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.studio.forceDelete', ['studio' => $studio]));

    $response->assertForbidden();
});

test('deleted', function () {
    $studio = Studio::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Studio::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.studio.forceDelete', ['studio' => $studio]));

    $response->assertOk();
    static::assertModelMissing($studio);
});
