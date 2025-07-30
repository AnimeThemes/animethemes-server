<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('anime', function () {
    $animeResource = AnimeResource::factory()
        ->for(Anime::factory())
        ->for(ExternalResource::factory(), 'resource')
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $animeResource->anime());
    $this->assertInstanceOf(Anime::class, $animeResource->anime()->first());
});

test('resource', function () {
    $animeResource = AnimeResource::factory()
        ->for(Anime::factory())
        ->for(ExternalResource::factory(), 'resource')
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $animeResource->resource());
    $this->assertInstanceOf(ExternalResource::class, $animeResource->resource()->first());
});
