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

    $this->assertInstanceOf(AnimeSeason::class, $season);
});

test('casts media format to enum', function () {
    $anime = Anime::factory()->createOne();

    $media_format = $anime->media_format;

    $this->assertInstanceOf(AnimeMediaFormat::class, $media_format);
});

test('searchable as', function () {
    $anime = Anime::factory()->createOne();

    $this->assertIsString($anime->searchableAs());
});

test('to searchable array', function () {
    $anime = Anime::factory()->createOne();

    $this->assertIsArray($anime->toSearchableArray());
});

test('nameable', function () {
    $anime = Anime::factory()->createOne();

    $this->assertIsString($anime->getName());
});

test('has subtitle', function () {
    $anime = Anime::factory()->createOne();

    $this->assertIsString($anime->getSubtitle());
});

test('synonyms', function () {
    $synonymCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(AnimeSynonym::factory()->count($synonymCount))
        ->createOne();

    $this->assertInstanceOf(HasMany::class, $anime->animesynonyms());
    $this->assertEquals($synonymCount, $anime->animesynonyms()->count());
    $this->assertInstanceOf(AnimeSynonym::class, $anime->animesynonyms()->first());
});

test('series', function () {
    $seriesCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(Series::factory()->count($seriesCount))
        ->createOne();

    $this->assertInstanceOf(BelongsToMany::class, $anime->series());
    $this->assertEquals($seriesCount, $anime->series()->count());
    $this->assertInstanceOf(Series::class, $anime->series()->first());
    $this->assertEquals(AnimeSeries::class, $anime->series()->getPivotClass());
});

test('themes', function () {
    $themeCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(AnimeTheme::factory()->count($themeCount))
        ->createOne();

    $this->assertInstanceOf(HasMany::class, $anime->animethemes());
    $this->assertEquals($themeCount, $anime->animethemes()->count());
    $this->assertInstanceOf(AnimeTheme::class, $anime->animethemes()->first());
});

test('external resources', function () {
    $resourceCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(ExternalResource::factory()->count($resourceCount), 'resources')
        ->createOne();

    $this->assertInstanceOf(BelongsToMany::class, $anime->resources());
    $this->assertEquals($resourceCount, $anime->resources()->count());
    $this->assertInstanceOf(ExternalResource::class, $anime->resources()->first());
    $this->assertEquals(AnimeResource::class, $anime->resources()->getPivotClass());
});

test('images', function () {
    $imageCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(Image::factory()->count($imageCount))
        ->createOne();

    $this->assertInstanceOf(BelongsToMany::class, $anime->images());
    $this->assertEquals($imageCount, $anime->images()->count());
    $this->assertInstanceOf(Image::class, $anime->images()->first());
    $this->assertEquals(AnimeImage::class, $anime->images()->getPivotClass());
});

test('studios', function () {
    $studioCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(Studio::factory()->count($studioCount))
        ->createOne();

    $this->assertInstanceOf(BelongsToMany::class, $anime->studios());
    $this->assertEquals($studioCount, $anime->studios()->count());
    $this->assertInstanceOf(Studio::class, $anime->studios()->first());
    $this->assertEquals(AnimeStudio::class, $anime->studios()->getPivotClass());
});
