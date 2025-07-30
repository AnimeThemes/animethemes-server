<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('artist', function () {
    $artistSong = ArtistSong::factory()
        ->for(Artist::factory())
        ->for(Song::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $artistSong->artist());
    $this->assertInstanceOf(Artist::class, $artistSong->artist()->first());
});

test('song', function () {
    $artistSong = ArtistSong::factory()
        ->for(Artist::factory())
        ->for(Song::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $artistSong->song());
    $this->assertInstanceOf(Song::class, $artistSong->song()->first());
});
