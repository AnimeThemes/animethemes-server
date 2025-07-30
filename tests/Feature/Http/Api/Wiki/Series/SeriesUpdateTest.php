<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Series;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

test('protected', function () {
    $series = Series::factory()->createOne();

    $parameters = Series::factory()->raw();

    $response = put(route('api.series.update', ['series' => $series] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $series = Series::factory()->createOne();

    $parameters = Series::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.series.update', ['series' => $series] + $parameters));

    $response->assertForbidden();
});

test('trashed', function () {
    $series = Series::factory()->trashed()->createOne();

    $parameters = Series::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Series::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.series.update', ['series' => $series] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $series = Series::factory()->createOne();

    $parameters = Series::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Series::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.series.update', ['series' => $series] + $parameters));

    $response->assertOk();
});
