<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\User\Like;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Video;
use App\Pivots\Morph\Resourceable;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Znck\Eloquent\Relations\BelongsToThrough;

uses(WithFaker::class);

test('searchable as', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $this->assertIsString($entry->searchableAs());
});

test('to searchable array', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $this->assertIsArray($entry->toSearchableArray());
});

test('nameable', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $this->assertIsString($entry->getName());
});

test('has subtitle', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $this->assertIsString($entry->getSubtitle());
});

test('theme', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $entry->animetheme());
    $this->assertInstanceOf(AnimeTheme::class, $entry->animetheme()->first());
});

test('external resources', function () {
    $resourcesCount = fake()->randomDigitNotNull();

    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->has(ExternalResource::factory()->count($resourcesCount), AnimeThemeEntry::RELATION_RESOURCES)
        ->createOne();

    $this->assertInstanceOf(MorphToMany::class, $entry->resources());
    $this->assertEquals($resourcesCount, $entry->resources()->count());
    $this->assertInstanceOf(ExternalResource::class, $entry->resources()->first());
    $this->assertEquals(Resourceable::class, $entry->resources()->getPivotClass());
});

test('videos', function () {
    $videoCount = fake()->randomDigitNotNull();

    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->has(Video::factory()->count($videoCount))
        ->createOne();

    $this->assertInstanceOf(BelongsToMany::class, $entry->videos());
    $this->assertEquals($videoCount, $entry->videos()->count());
    $this->assertInstanceOf(Video::class, $entry->videos()->first());
    $this->assertEquals(AnimeThemeEntryVideo::class, $entry->videos()->getPivotClass());
});

test('anime', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $this->assertInstanceOf(BelongsToThrough::class, $entry->anime());
    $this->assertInstanceOf(Anime::class, $entry->anime()->first());
});

test('likes', function () {
    $entry = AnimeThemeEntry::factory()
        ->for(Like::factory()->for(User::factory()))
        ->createOne();

    $this->assertInstanceOf(MorphMany::class, $entry->likes());
    $this->assertInstanceOf(Like::class, $entry->likes()->first());
});
