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

    $this->assertInstanceOf(BelongsTo::class, $animeStudio->anime());
    $this->assertInstanceOf(Anime::class, $animeStudio->anime()->first());
});

test('studio', function () {
    $animeStudio = AnimeStudio::factory()
        ->for(Anime::factory())
        ->for(Studio::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $animeStudio->studio());
    $this->assertInstanceOf(Studio::class, $animeStudio->studio()->first());
});
