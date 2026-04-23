<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Group;
use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('casts type to enum', function (): void {
    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    $type = $theme->type;

    $this->assertInstanceOf(ThemeType::class, $type);
});

test('searchable as', function (): void {
    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    $this->assertIsString($theme->searchableAs());
});

test('to searchable array', function (): void {
    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    $this->assertIsArray($theme->toSearchableArray());
});

test('nameable', function (): void {
    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    $this->assertIsString($theme->getName());
});

test('has subtitle', function (): void {
    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    $this->assertIsString($theme->getSubtitle());
});

test('anime', function (): void {
    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $theme->anime());
    $this->assertInstanceOf(Anime::class, $theme->anime()->first());
});

test('group', function (): void {
    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->for(Group::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $theme->group());
    $this->assertInstanceOf(Group::class, $theme->group()->first());
});

test('song', function (): void {
    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->for(Song::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $theme->song());
    $this->assertInstanceOf(Song::class, $theme->song()->first());
});

test('entries', function (): void {
    $entryCount = fake()->randomDigitNotNull();

    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(AnimeThemeEntry::factory()->count($entryCount))
        ->createOne();

    $this->assertInstanceOf(HasMany::class, $theme->animethemeentries());
    $this->assertEquals($entryCount, $theme->animethemeentries()->count());
    $this->assertInstanceOf(AnimeThemeEntry::class, $theme->animethemeentries()->first());
});

test('theme creates slug', function (): void {
    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    $this->assertArrayHasKey('slug', $theme);
});
