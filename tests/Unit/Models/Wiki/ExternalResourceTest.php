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

    $this->assertInstanceOf(ResourceSite::class, $site);
});

test('nameable', function () {
    $resource = ExternalResource::factory()->createOne();

    $this->assertIsString($resource->getName());
});

test('has subtitle', function () {
    $resource = ExternalResource::factory()->createOne();

    $this->assertIsString($resource->getSubtitle());
});

test('anime', function () {
    $animeCount = fake()->randomDigitNotNull();

    $resource = ExternalResource::factory()
        ->has(Anime::factory()->count($animeCount))
        ->createOne();

    $this->assertInstanceOf(BelongsToMany::class, $resource->anime());
    $this->assertEquals($animeCount, $resource->anime()->count());
    $this->assertInstanceOf(Anime::class, $resource->anime()->first());
    $this->assertEquals(AnimeResource::class, $resource->anime()->getPivotClass());
});

test('artists', function () {
    $artistCount = fake()->randomDigitNotNull();

    $resource = ExternalResource::factory()
        ->has(Artist::factory()->count($artistCount))
        ->createOne();

    $this->assertInstanceOf(BelongsToMany::class, $resource->artists());
    $this->assertEquals($artistCount, $resource->artists()->count());
    $this->assertInstanceOf(Artist::class, $resource->artists()->first());
    $this->assertEquals(ArtistResource::class, $resource->artists()->getPivotClass());
});

test('song', function () {
    $songCount = fake()->randomDigitNotNull();

    $resource = ExternalResource::factory()
        ->has(Song::factory()->count($songCount))
        ->createOne();

    $this->assertInstanceOf(BelongsToMany::class, $resource->songs());
    $this->assertEquals($songCount, $resource->songs()->count());
    $this->assertInstanceOf(Song::class, $resource->songs()->first());
    $this->assertEquals(SongResource::class, $resource->songs()->getPivotClass());
});

test('studio', function () {
    $studioCount = fake()->randomDigitNotNull();

    $resource = ExternalResource::factory()
        ->has(Studio::factory()->count($studioCount))
        ->createOne();

    $this->assertInstanceOf(BelongsToMany::class, $resource->studios());
    $this->assertEquals($studioCount, $resource->studios()->count());
    $this->assertInstanceOf(Studio::class, $resource->studios()->first());
    $this->assertEquals(StudioResource::class, $resource->studios()->getPivotClass());
});
