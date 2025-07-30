<?php

declare(strict_types=1);

use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\SongResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('song', function () {
    $songResource = SongResource::factory()
        ->for(Song::factory())
        ->for(ExternalResource::factory(), 'resource')
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $songResource->song());
    static::assertInstanceOf(Song::class, $songResource->song()->first());
});

test('resource', function () {
    $songResource = SongResource::factory()
        ->for(Song::factory())
        ->for(ExternalResource::factory(), 'resource')
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $songResource->resource());
    static::assertInstanceOf(ExternalResource::class, $songResource->resource()->first());
});
