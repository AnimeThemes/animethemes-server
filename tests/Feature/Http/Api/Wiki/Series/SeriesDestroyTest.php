<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Series;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function (): void {
    $series = Series::factory()->createOne();

    $response = delete(route('api.series.destroy', ['series' => $series]));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $series = Series::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.series.destroy', ['series' => $series]));

    $response->assertForbidden();
});

test('trashed', function (): void {
    $series = Series::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Series::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.series.destroy', ['series' => $series]));

    $response->assertNotFound();
});

test('deleted', function (): void {
    $series = Series::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Series::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.series.destroy', ['series' => $series]));

    $response->assertOk();
    $this->assertSoftDeleted($series);
});
