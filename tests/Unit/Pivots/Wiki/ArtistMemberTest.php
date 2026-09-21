<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('artist', function (): void {
    $artistMember = ArtistMember::factory()
        ->for(Artist::factory(), 'artist')
        ->for(Artist::factory(), 'member')
        ->createOne();

    expect($artistMember->artist())->toBeInstanceOf(BelongsTo::class);
    expect($artistMember->artist()->first())->toBeInstanceOf(Artist::class);
});

test('member', function (): void {
    $artistMember = ArtistMember::factory()
        ->for(Artist::factory(), 'artist')
        ->for(Artist::factory(), 'member')
        ->createOne();

    expect($artistMember->member())->toBeInstanceOf(BelongsTo::class);
    expect($artistMember->member()->first())->toBeInstanceOf(Artist::class);
});
