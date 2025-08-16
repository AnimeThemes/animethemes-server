<?php

declare(strict_types=1);

use App\Models\List\Playlist;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Morph\Imageable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

test('image', function () {
    $imageable = Imageable::factory()
        ->for(Image::factory(), Imageable::RELATION_IMAGE)
        ->forAnime()
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $imageable->image());
    $this->assertInstanceOf(Image::class, $imageable->image()->first());
});

test('imageable playlist', function () {
    $imageable = Imageable::factory()
        ->for(Image::factory(), Imageable::RELATION_IMAGE)
        ->forPlaylist()
        ->createOne();

    $this->assertInstanceOf(MorphTo::class, $imageable->imageable());
    $this->assertInstanceOf(Playlist::class, $imageable->imageable()->first());
});

test('imageable anime', function () {
    $imageable = Imageable::factory()
        ->for(Image::factory(), Imageable::RELATION_IMAGE)
        ->forAnime()
        ->createOne();

    $this->assertInstanceOf(MorphTo::class, $imageable->imageable());
    $this->assertInstanceOf(Anime::class, $imageable->imageable()->first());
});

test('imageable artist', function () {
    $imageable = Imageable::factory()
        ->for(Image::factory(), Imageable::RELATION_IMAGE)
        ->forArtist()
        ->createOne();

    $this->assertInstanceOf(MorphTo::class, $imageable->imageable());
    $this->assertInstanceOf(Artist::class, $imageable->imageable()->first());
});

test('imageable studio', function () {
    $imageable = Imageable::factory()
        ->for(Image::factory(), Imageable::RELATION_IMAGE)
        ->forStudio()
        ->createOne();

    $this->assertInstanceOf(MorphTo::class, $imageable->imageable());
    $this->assertInstanceOf(Studio::class, $imageable->imageable()->first());
});
