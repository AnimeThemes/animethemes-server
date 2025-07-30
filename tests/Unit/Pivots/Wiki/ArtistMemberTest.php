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

    $this->assertInstanceOf(BelongsTo::class, $artistMember->artist());
    $this->assertInstanceOf(Artist::class, $artistMember->artist()->first());
});

test('member', function () {
    $artistMember = ArtistMember::factory()
        ->for(Artist::factory(), 'artist')
        ->for(Artist::factory(), 'member')
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $artistMember->member());
    $this->assertInstanceOf(Artist::class, $artistMember->member()->first());
});
