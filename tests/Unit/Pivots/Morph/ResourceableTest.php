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

test('resource', function () {
    $resourceable = Resourceable::factory()
        ->for(ExternalResource::factory(), Resourceable::RELATION_RESOURCE)
        ->for(Anime::factory(), Resourceable::RELATION_RESOURCEABLE)
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $resourceable->resource());
    $this->assertInstanceOf(ExternalResource::class, $resourceable->resource()->first());
});

test('resourceable anime', function () {
    $resourceable = Resourceable::factory()
        ->for(ExternalResource::factory(), Resourceable::RELATION_RESOURCE)
        ->forAnime()
        ->createOne();

    $this->assertInstanceOf(MorphTo::class, $resourceable->resourceable());
    $this->assertInstanceOf(Anime::class, $resourceable->resourceable()->first());
});

test('resourceable artist', function () {
    $resourceable = Resourceable::factory()
        ->for(ExternalResource::factory(), Resourceable::RELATION_RESOURCE)
        ->forArtist()
        ->createOne();

    $this->assertInstanceOf(MorphTo::class, $resourceable->resourceable());
    $this->assertInstanceOf(Artist::class, $resourceable->resourceable()->first());
});

test('resourceable song', function () {
    $resourceable = Resourceable::factory()
        ->for(ExternalResource::factory(), Resourceable::RELATION_RESOURCE)
        ->forSong()
        ->createOne();

    $this->assertInstanceOf(MorphTo::class, $resourceable->resourceable());
    $this->assertInstanceOf(Song::class, $resourceable->resourceable()->first());
});

test('resourceable studio', function () {
    $resourceable = Resourceable::factory()
        ->for(ExternalResource::factory(), Resourceable::RELATION_RESOURCE)
        ->forStudio()
        ->createOne();

    $this->assertInstanceOf(MorphTo::class, $resourceable->resourceable());
    $this->assertInstanceOf(Studio::class, $resourceable->resourceable()->first());
});
