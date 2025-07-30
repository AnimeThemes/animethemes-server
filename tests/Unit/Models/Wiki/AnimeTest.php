<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeImage;
use App\Pivots\Wiki\AnimeResource;
use App\Pivots\Wiki\AnimeSeries;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('casts season to enum', function () {
    $anime = Anime::factory()->createOne();

    $season = $anime->season;

    static::assertInstanceOf(AnimeSeason::class, $season);
});

test('casts media format to enum', function () {
    $anime = Anime::factory()->createOne();

    $media_format = $anime->media_format;

    static::assertInstanceOf(AnimeMediaFormat::class, $media_format);
});

test('searchable as', function () {
    $anime = Anime::factory()->createOne();

    static::assertIsString($anime->searchableAs());
});

test('to searchable array', function () {
    $anime = Anime::factory()->createOne();

    static::assertIsArray($anime->toSearchableArray());
});

test('nameable', function () {
    $anime = Anime::factory()->createOne();

    static::assertIsString($anime->getName());
});

test('has subtitle', function () {
    $anime = Anime::factory()->createOne();

    static::assertIsString($anime->getSubtitle());
});

test('synonyms', function () {
    $synonymCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(AnimeSynonym::factory()->count($synonymCount))
        ->createOne();

    static::assertInstanceOf(HasMany::class, $anime->animesynonyms());
    static::assertEquals($synonymCount, $anime->animesynonyms()->count());
    static::assertInstanceOf(AnimeSynonym::class, $anime->animesynonyms()->first());
});

test('series', function () {
    $seriesCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(Series::factory()->count($seriesCount))
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $anime->series());
    static::assertEquals($seriesCount, $anime->series()->count());
    static::assertInstanceOf(Series::class, $anime->series()->first());
    static::assertEquals(AnimeSeries::class, $anime->series()->getPivotClass());
});

test('themes', function () {
    $themeCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(AnimeTheme::factory()->count($themeCount))
        ->createOne();

    static::assertInstanceOf(HasMany::class, $anime->animethemes());
    static::assertEquals($themeCount, $anime->animethemes()->count());
    static::assertInstanceOf(AnimeTheme::class, $anime->animethemes()->first());
});

test('external resources', function () {
    $resourceCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(ExternalResource::factory()->count($resourceCount), 'resources')
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $anime->resources());
    static::assertEquals($resourceCount, $anime->resources()->count());
    static::assertInstanceOf(ExternalResource::class, $anime->resources()->first());
    static::assertEquals(AnimeResource::class, $anime->resources()->getPivotClass());
});

test('images', function () {
    $imageCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(Image::factory()->count($imageCount))
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $anime->images());
    static::assertEquals($imageCount, $anime->images()->count());
    static::assertInstanceOf(Image::class, $anime->images()->first());
    static::assertEquals(AnimeImage::class, $anime->images()->getPivotClass());
});

test('studios', function () {
    $studioCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(Studio::factory()->count($studioCount))
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $anime->studios());
    static::assertEquals($studioCount, $anime->studios()->count());
    static::assertInstanceOf(Studio::class, $anime->studios()->first());
    static::assertEquals(AnimeStudio::class, $anime->studios()->getPivotClass());
});
