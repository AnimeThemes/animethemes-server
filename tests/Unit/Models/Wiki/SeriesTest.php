<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('searchable as', function (): void {
    $series = Series::factory()->createOne();

    expect($series->searchableAs())->toBeString();
});

test('to searchable array', function (): void {
    $series = Series::factory()->createOne();

    expect($series->toSearchableArray())->toBeArray();
});

test('nameable', function (): void {
    $series = Series::factory()->createOne();

    expect($series->getName())->toBeString();
});

test('has subtitle', function (): void {
    $series = Series::factory()->createOne();

    expect($series->getSubtitle())->toBeString();
});

test('anime', function (): void {
    $animeCount = fake()->randomDigitNotNull();

    $series = Series::factory()
        ->has(Anime::factory()->count($animeCount))
        ->createOne();

    expect($series->anime())->toBeInstanceOf(BelongsToMany::class);
    expect($series->anime()->count())->toEqual($animeCount);
    expect($series->anime()->first())->toBeInstanceOf(Anime::class);
    expect($series->anime()->getPivotClass())->toEqual(AnimeSeries::class);
});
