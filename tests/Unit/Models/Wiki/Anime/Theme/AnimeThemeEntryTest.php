<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Znck\Eloquent\Relations\BelongsToThrough;

uses(WithFaker::class);

test('searchable as', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    static::assertIsString($entry->searchableAs());
});

test('to searchable array', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    static::assertIsArray($entry->toSearchableArray());
});

test('nameable', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    static::assertIsString($entry->getName());
});

test('has subtitle', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    static::assertIsString($entry->getSubtitle());
});

test('theme', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $entry->animetheme());
    static::assertInstanceOf(AnimeTheme::class, $entry->animetheme()->first());
});

test('videos', function () {
    $videoCount = fake()->randomDigitNotNull();

    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->has(Video::factory()->count($videoCount))
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $entry->videos());
    static::assertEquals($videoCount, $entry->videos()->count());
    static::assertInstanceOf(Video::class, $entry->videos()->first());
    static::assertEquals(AnimeThemeEntryVideo::class, $entry->videos()->getPivotClass());
});

test('anime', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    static::assertInstanceOf(BelongsToThrough::class, $entry->anime());
    static::assertInstanceOf(Anime::class, $entry->anime()->first());
});
