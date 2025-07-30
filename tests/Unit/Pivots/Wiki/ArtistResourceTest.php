<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\ArtistResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('artist', function () {
    $artistResource = ArtistResource::factory()
        ->for(Artist::factory())
        ->for(ExternalResource::factory(), 'resource')
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $artistResource->artist());
    static::assertInstanceOf(Artist::class, $artistResource->artist()->first());
});

test('resource', function () {
    $artistResource = ArtistResource::factory()
        ->for(Artist::factory())
        ->for(ExternalResource::factory(), 'resource')
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $artistResource->resource());
    static::assertInstanceOf(ExternalResource::class, $artistResource->resource()->first());
});
