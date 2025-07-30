<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('anime', function () {
    $animeStudio = AnimeStudio::factory()
        ->for(Anime::factory())
        ->for(Studio::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $animeStudio->anime());
    static::assertInstanceOf(Anime::class, $animeStudio->anime()->first());
});

test('studio', function () {
    $animeStudio = AnimeStudio::factory()
        ->for(Anime::factory())
        ->for(Studio::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $animeStudio->studio());
    static::assertInstanceOf(Studio::class, $animeStudio->studio()->first());
});
