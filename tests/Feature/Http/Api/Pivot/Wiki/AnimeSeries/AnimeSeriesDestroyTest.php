<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $animeSeries = AnimeSeries::factory()
        ->for(Anime::factory())
        ->for(Series::factory())
        ->createOne();

    $response = delete(route('api.animeseries.destroy', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $animeSeries = AnimeSeries::factory()
        ->for(Anime::factory())
        ->for(Series::factory())
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animeseries.destroy', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series]));

    $response->assertForbidden();
});

test('not found', function () {
    $anime = Anime::factory()->createOne();
    $series = Series::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Anime::class),
            CrudPermission::DELETE->format(Series::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animeseries.destroy', ['anime' => $anime, 'series' => $series]));

    $response->assertNotFound();
});

test('deleted', function () {
    $animeSeries = AnimeSeries::factory()
        ->for(Anime::factory())
        ->for(Series::factory())
        ->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Anime::class),
            CrudPermission::DELETE->format(Series::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animeseries.destroy', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series]));

    $response->assertOk();
    $this->assertModelMissing($animeSeries);
});
