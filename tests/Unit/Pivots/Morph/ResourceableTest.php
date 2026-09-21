<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Pivots\Morph\Resourceable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

test('resource', function (): void {
    $resourceable = Resourceable::factory()
        ->for(ExternalResource::factory(), Resourceable::RELATION_RESOURCE)
        ->for(Anime::factory(), Resourceable::RELATION_RESOURCEABLE)
        ->createOne();

    expect($resourceable->resource())->toBeInstanceOf(BelongsTo::class);
    expect($resourceable->resource()->first())->toBeInstanceOf(ExternalResource::class);
});

test('resourceable anime', function (): void {
    $resourceable = Resourceable::factory()
        ->for(ExternalResource::factory(), Resourceable::RELATION_RESOURCE)
        ->forAnime()
        ->createOne();

    expect($resourceable->resourceable())->toBeInstanceOf(MorphTo::class);
    expect($resourceable->resourceable()->first())->toBeInstanceOf(Anime::class);
});

test('resourceable artist', function (): void {
    $resourceable = Resourceable::factory()
        ->for(ExternalResource::factory(), Resourceable::RELATION_RESOURCE)
        ->forArtist()
        ->createOne();

    expect($resourceable->resourceable())->toBeInstanceOf(MorphTo::class);
    expect($resourceable->resourceable()->first())->toBeInstanceOf(Artist::class);
});

test('resourceable song', function (): void {
    $resourceable = Resourceable::factory()
        ->for(ExternalResource::factory(), Resourceable::RELATION_RESOURCE)
        ->forSong()
        ->createOne();

    expect($resourceable->resourceable())->toBeInstanceOf(MorphTo::class);
    expect($resourceable->resourceable()->first())->toBeInstanceOf(Song::class);
});

test('resourceable studio', function (): void {
    $resourceable = Resourceable::factory()
        ->for(ExternalResource::factory(), Resourceable::RELATION_RESOURCE)
        ->forStudio()
        ->createOne();

    expect($resourceable->resourceable())->toBeInstanceOf(MorphTo::class);
    expect($resourceable->resourceable()->first())->toBeInstanceOf(Studio::class);
});
