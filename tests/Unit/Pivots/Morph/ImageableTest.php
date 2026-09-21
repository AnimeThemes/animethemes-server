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

test('image', function (): void {
    $imageable = Imageable::factory()
        ->for(Image::factory(), Imageable::RELATION_IMAGE)
        ->forAnime()
        ->createOne();

    expect($imageable->image())->toBeInstanceOf(BelongsTo::class);
    expect($imageable->image()->first())->toBeInstanceOf(Image::class);
});

test('imageable playlist', function (): void {
    $imageable = Imageable::factory()
        ->for(Image::factory(), Imageable::RELATION_IMAGE)
        ->forPlaylist()
        ->createOne();

    expect($imageable->imageable())->toBeInstanceOf(MorphTo::class);
    expect($imageable->imageable()->first())->toBeInstanceOf(Playlist::class);
});

test('imageable anime', function (): void {
    $imageable = Imageable::factory()
        ->for(Image::factory(), Imageable::RELATION_IMAGE)
        ->forAnime()
        ->createOne();

    expect($imageable->imageable())->toBeInstanceOf(MorphTo::class);
    expect($imageable->imageable()->first())->toBeInstanceOf(Anime::class);
});

test('imageable artist', function (): void {
    $imageable = Imageable::factory()
        ->for(Image::factory(), Imageable::RELATION_IMAGE)
        ->forArtist()
        ->createOne();

    expect($imageable->imageable())->toBeInstanceOf(MorphTo::class);
    expect($imageable->imageable()->first())->toBeInstanceOf(Artist::class);
});

test('imageable studio', function (): void {
    $imageable = Imageable::factory()
        ->for(Image::factory(), Imageable::RELATION_IMAGE)
        ->forStudio()
        ->createOne();

    expect($imageable->imageable())->toBeInstanceOf(MorphTo::class);
    expect($imageable->imageable()->first())->toBeInstanceOf(Studio::class);
});
