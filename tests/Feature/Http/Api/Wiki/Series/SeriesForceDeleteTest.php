<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Series;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $series = Series::factory()->createOne();

    $response = $this->delete(route('api.series.forceDelete', ['series' => $series]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $series = Series::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.series.forceDelete', ['series' => $series]));

    $response->assertForbidden();
});

test('deleted', function () {
    $series = Series::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Series::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.series.forceDelete', ['series' => $series]));

    $response->assertOk();
    static::assertModelMissing($series);
});
