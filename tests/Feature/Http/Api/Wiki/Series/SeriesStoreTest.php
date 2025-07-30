<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Series;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $series = Series::factory()->makeOne();

    $response = $this->post(route('api.series.store', $series->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $series = Series::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.series.store', $series->toArray()));

    $response->assertForbidden();
});

test('required fields', function () {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Series::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.series.store'));

    $response->assertJsonValidationErrors([
        Series::ATTRIBUTE_NAME,
        Series::ATTRIBUTE_SLUG,
    ]);
});

test('create', function () {
    $parameters = Series::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Series::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.series.store', $parameters));

    $response->assertCreated();
    static::assertDatabaseCount(Series::class, 1);
});
