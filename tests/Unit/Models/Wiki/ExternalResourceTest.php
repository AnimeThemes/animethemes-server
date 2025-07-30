<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeResource;
use App\Pivots\Wiki\ArtistResource;
use App\Pivots\Wiki\SongResource;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('casts season to enum', function () {
    $resource = ExternalResource::factory()->createOne();

    $site = $resource->site;

    static::assertInstanceOf(ResourceSite::class, $site);
});

test('nameable', function () {
    $resource = ExternalResource::factory()->createOne();

    static::assertIsString($resource->getName());
});

test('has subtitle', function () {
    $resource = ExternalResource::factory()->createOne();

    static::assertIsString($resource->getSubtitle());
});

test('anime', function () {
    $animeCount = fake()->randomDigitNotNull();

    $resource = ExternalResource::factory()
        ->has(Anime::factory()->count($animeCount))
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $resource->anime());
    static::assertEquals($animeCount, $resource->anime()->count());
    static::assertInstanceOf(Anime::class, $resource->anime()->first());
    static::assertEquals(AnimeResource::class, $resource->anime()->getPivotClass());
});

test('artists', function () {
    $artistCount = fake()->randomDigitNotNull();

    $resource = ExternalResource::factory()
        ->has(Artist::factory()->count($artistCount))
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $resource->artists());
    static::assertEquals($artistCount, $resource->artists()->count());
    static::assertInstanceOf(Artist::class, $resource->artists()->first());
    static::assertEquals(ArtistResource::class, $resource->artists()->getPivotClass());
});

test('song', function () {
    $songCount = fake()->randomDigitNotNull();

    $resource = ExternalResource::factory()
        ->has(Song::factory()->count($songCount))
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $resource->songs());
    static::assertEquals($songCount, $resource->songs()->count());
    static::assertInstanceOf(Song::class, $resource->songs()->first());
    static::assertEquals(SongResource::class, $resource->songs()->getPivotClass());
});

test('studio', function () {
    $studioCount = fake()->randomDigitNotNull();

    $resource = ExternalResource::factory()
        ->has(Studio::factory()->count($studioCount))
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $resource->studios());
    static::assertEquals($studioCount, $resource->studios()->count());
    static::assertInstanceOf(Studio::class, $resource->studios()->first());
    static::assertEquals(StudioResource::class, $resource->studios()->getPivotClass());
});
