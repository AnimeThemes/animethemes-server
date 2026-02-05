<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Performance;
use App\Pivots\Morph\Resourceable;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('searchable as', function () {
    $song = Song::factory()->createOne();

    $this->assertIsString($song->searchableAs());
});

test('to searchable array', function () {
    $song = Song::factory()->createOne();

    $this->assertIsArray($song->toSearchableArray());
});

test('nameable', function () {
    $song = Song::factory()->createOne();

    $this->assertIsString($song->getName());
});

test('has subtitle', function () {
    $song = Song::factory()
        ->has(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $this->assertIsString($song->getSubtitle());
});

test('themes', function () {
    $themeCount = fake()->randomDigitNotNull();

    $song = Song::factory()
        ->has(AnimeTheme::factory()->for(Anime::factory())->count($themeCount))
        ->createOne();

    $this->assertInstanceOf(HasMany::class, $song->animethemes());
    $this->assertEquals($themeCount, $song->animethemes()->count());
    $this->assertInstanceOf(AnimeTheme::class, $song->animethemes()->first());
});

test('artists', function () {
    $artistCount = fake()->randomDigitNotNull();

    $song = Song::factory()
        ->has(Artist::factory()->count($artistCount))
        ->createOne();

    $this->assertInstanceOf(BelongsToMany::class, $song->artists());
    $this->assertEquals($artistCount, $song->artists()->count());
    $this->assertInstanceOf(Artist::class, $song->artists()->first());
    $this->assertEquals(ArtistSong::class, $song->artists()->getPivotClass());
});

test('performances', function () {
    $performanceCount = fake()->randomDigitNotNull();

    $song = Song::factory()
        ->has(Performance::factory()->count($performanceCount)->artist(Artist::factory()->createOne()))
        ->createOne();

    $this->assertInstanceOf(HasMany::class, $song->performances());
    $this->assertEquals($performanceCount, $song->performances()->count());
    $this->assertInstanceOf(Performance::class, $song->performances()->first());
});

test('external resources', function () {
    $resourceCount = fake()->randomDigitNotNull();

    $song = Song::factory()
        ->has(ExternalResource::factory()->count($resourceCount), 'resources')
        ->createOne();

    $this->assertInstanceOf(MorphToMany::class, $song->resources());
    $this->assertEquals($resourceCount, $song->resources()->count());
    $this->assertInstanceOf(ExternalResource::class, $song->resources()->first());
    $this->assertEquals(Resourceable::class, $song->resources()->getPivotClass());
});
