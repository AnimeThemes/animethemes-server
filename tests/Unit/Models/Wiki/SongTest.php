<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
use App\Pivots\Wiki\SongResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('searchable as', function () {
    $song = Song::factory()->createOne();

    static::assertIsString($song->searchableAs());
});

test('to searchable array', function () {
    $song = Song::factory()->createOne();

    static::assertIsArray($song->toSearchableArray());
});

test('nameable', function () {
    $song = Song::factory()->createOne();

    static::assertIsString($song->getName());
});

test('has subtitle', function () {
    $song = Song::factory()
        ->has(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    static::assertIsString($song->getSubtitle());
});

test('themes', function () {
    $themeCount = fake()->randomDigitNotNull();

    $song = Song::factory()
        ->has(AnimeTheme::factory()->for(Anime::factory())->count($themeCount))
        ->createOne();

    static::assertInstanceOf(HasMany::class, $song->animethemes());
    static::assertEquals($themeCount, $song->animethemes()->count());
    static::assertInstanceOf(AnimeTheme::class, $song->animethemes()->first());
});

test('artists', function () {
    $artistCount = fake()->randomDigitNotNull();

    $song = Song::factory()
        ->has(Artist::factory()->count($artistCount))
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $song->artists());
    static::assertEquals($artistCount, $song->artists()->count());
    static::assertInstanceOf(Artist::class, $song->artists()->first());
    static::assertEquals(ArtistSong::class, $song->artists()->getPivotClass());
});

test('external resources', function () {
    $resourceCount = fake()->randomDigitNotNull();

    $song = Song::factory()
        ->has(ExternalResource::factory()->count($resourceCount), 'resources')
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $song->resources());
    static::assertEquals($resourceCount, $song->resources()->count());
    static::assertInstanceOf(ExternalResource::class, $song->resources()->first());
    static::assertEquals(SongResource::class, $song->resources()->getPivotClass());
});
