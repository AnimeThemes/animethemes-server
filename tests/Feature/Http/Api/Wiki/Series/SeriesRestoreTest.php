<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Series;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function () {
    $series = Series::factory()->trashed()->createOne();

    $response = patch(route('api.series.restore', ['series' => $series]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $series = Series::factory()->trashed()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.series.restore', ['series' => $series]));

    $response->assertForbidden();
});

test('trashed', function () {
    $series = Series::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Series::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.series.restore', ['series' => $series]));

    $response->assertForbidden();
});

test('restored', function () {
    $series = Series::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Series::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.series.restore', ['series' => $series]));

    $response->assertOk();
    $this->assertNotSoftDeleted($series);
});
