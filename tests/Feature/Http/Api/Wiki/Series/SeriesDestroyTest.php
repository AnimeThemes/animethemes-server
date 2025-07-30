<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Series;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $series = Series::factory()->createOne();

    $response = $this->delete(route('api.series.destroy', ['series' => $series]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $series = Series::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.series.destroy', ['series' => $series]));

    $response->assertForbidden();
});

test('trashed', function () {
    $series = Series::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Series::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.series.destroy', ['series' => $series]));

    $response->assertNotFound();
});

test('deleted', function () {
    $series = Series::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Series::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.series.destroy', ['series' => $series]));

    $response->assertOk();
    static::assertSoftDeleted($series);
});
