<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('anime', function (): void {
    $animeStudio = AnimeStudio::factory()
        ->for(Anime::factory())
        ->for(Studio::factory())
        ->createOne();

    expect($animeStudio->anime())->toBeInstanceOf(BelongsTo::class);
    expect($animeStudio->anime()->first())->toBeInstanceOf(Anime::class);
});

test('studio', function (): void {
    $animeStudio = AnimeStudio::factory()
        ->for(Anime::factory())
        ->for(Studio::factory())
        ->createOne();

    expect($animeStudio->studio())->toBeInstanceOf(BelongsTo::class);
    expect($animeStudio->studio()->first())->toBeInstanceOf(Studio::class);
});
