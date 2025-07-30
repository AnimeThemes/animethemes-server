<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Series;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $series = Series::factory()->createOne();

    $response = delete(route('api.series.forceDelete', ['series' => $series]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $series = Series::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.series.forceDelete', ['series' => $series]));

    $response->assertForbidden();
});

test('deleted', function () {
    $series = Series::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Series::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.series.forceDelete', ['series' => $series]));

    $response->assertOk();
    $this->assertModelMissing($series);
});
