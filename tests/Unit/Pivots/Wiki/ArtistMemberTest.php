<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('artist', function () {
    $artistMember = ArtistMember::factory()
        ->for(Artist::factory(), 'artist')
        ->for(Artist::factory(), 'member')
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $artistMember->artist());
    static::assertInstanceOf(Artist::class, $artistMember->artist()->first());
});

test('member', function () {
    $artistMember = ArtistMember::factory()
        ->for(Artist::factory(), 'artist')
        ->for(Artist::factory(), 'member')
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $artistMember->member());
    static::assertInstanceOf(Artist::class, $artistMember->member()->first());
});
