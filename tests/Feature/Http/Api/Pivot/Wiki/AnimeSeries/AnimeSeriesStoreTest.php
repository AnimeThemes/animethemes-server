<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $anime = Anime::factory()->createOne();
    $series = Series::factory()->createOne();

    $response = post(route('api.animeseries.store', ['anime' => $anime, 'series' => $series]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $anime = Anime::factory()->createOne();
    $series = Series::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animeseries.store', ['anime' => $anime, 'series' => $series]));

    $response->assertForbidden();
});

test('create', function () {
    $anime = Anime::factory()->createOne();
    $series = Series::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(Anime::class),
            CrudPermission::CREATE->format(Series::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animeseries.store', ['anime' => $anime, 'series' => $series]));

    $response->assertCreated();
    $this->assertDatabaseCount(AnimeSeries::class, 1);
});
