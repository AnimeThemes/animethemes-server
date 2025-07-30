<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('searchable as', function () {
    $series = Series::factory()->createOne();

    static::assertIsString($series->searchableAs());
});

test('to searchable array', function () {
    $series = Series::factory()->createOne();

    static::assertIsArray($series->toSearchableArray());
});

test('nameable', function () {
    $series = Series::factory()->createOne();

    static::assertIsString($series->getName());
});

test('has subtitle', function () {
    $series = Series::factory()->createOne();

    static::assertIsString($series->getSubtitle());
});

test('anime', function () {
    $animeCount = fake()->randomDigitNotNull();

    $series = Series::factory()
        ->has(Anime::factory()->count($animeCount))
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $series->anime());
    static::assertEquals($animeCount, $series->anime()->count());
    static::assertInstanceOf(Anime::class, $series->anime()->first());
    static::assertEquals(AnimeSeries::class, $series->anime()->getPivotClass());
});
