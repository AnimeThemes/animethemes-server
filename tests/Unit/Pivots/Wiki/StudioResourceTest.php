<?php

declare(strict_types=1);

use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('studio', function () {
    $studioResource = StudioResource::factory()
        ->for(Studio::factory())
        ->for(ExternalResource::factory(), 'resource')
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $studioResource->studio());
    static::assertInstanceOf(Studio::class, $studioResource->studio()->first());
});

test('resource', function () {
    $studioResource = StudioResource::factory()
        ->for(Studio::factory())
        ->for(ExternalResource::factory(), 'resource')
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $studioResource->resource());
    static::assertInstanceOf(ExternalResource::class, $studioResource->resource()->first());
});
