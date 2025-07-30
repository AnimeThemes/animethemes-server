<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('anime', function () {
    $animeSeries = AnimeSeries::factory()
        ->for(Anime::factory())
        ->for(Series::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $animeSeries->anime());
    $this->assertInstanceOf(Anime::class, $animeSeries->anime()->first());
});

test('series', function () {
    $animeSeries = AnimeSeries::factory()
        ->for(Anime::factory())
        ->for(Series::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $animeSeries->series());
    $this->assertInstanceOf(Series::class, $animeSeries->series()->first());
});
