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

    $this->assertInstanceOf(BelongsTo::class, $artistResource->artist());
    $this->assertInstanceOf(Artist::class, $artistResource->artist()->first());
});

test('resource', function () {
    $artistResource = ArtistResource::factory()
        ->for(Artist::factory())
        ->for(ExternalResource::factory(), 'resource')
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $artistResource->resource());
    $this->assertInstanceOf(ExternalResource::class, $artistResource->resource()->first());
});
