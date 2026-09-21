<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('anime', function (): void {
    $animeSeries = AnimeSeries::factory()
        ->for(Anime::factory())
        ->for(Series::factory())
        ->createOne();

    expect($animeSeries->anime())->toBeInstanceOf(BelongsTo::class);
    expect($animeSeries->anime()->first())->toBeInstanceOf(Anime::class);
});

test('series', function (): void {
    $animeSeries = AnimeSeries::factory()
        ->for(Anime::factory())
        ->for(Series::factory())
        ->createOne();

    expect($animeSeries->series())->toBeInstanceOf(BelongsTo::class);
    expect($animeSeries->series()->first())->toBeInstanceOf(Series::class);
});
