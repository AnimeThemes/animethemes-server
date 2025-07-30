<?php

declare(strict_types=1);

use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

test('nameable', function () {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    static::assertIsString($featuredTheme->getName());
});

test('has subtitle', function () {
    $featuredTheme = FeaturedTheme::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->createOne();

    static::assertIsString($featuredTheme->getSubtitle());
});

test('casts end at', function () {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    static::assertInstanceOf(Carbon::class, $featuredTheme->end_at);
});

test('casts start at', function () {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    static::assertInstanceOf(Carbon::class, $featuredTheme->start_at);
});

test('user', function () {
    $featuredTheme = FeaturedTheme::factory()
        ->for(User::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $featuredTheme->user());
    static::assertInstanceOf(User::class, $featuredTheme->user()->first());
});

test('video', function () {
    $featuredTheme = FeaturedTheme::factory()
        ->for(Video::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $featuredTheme->video());
    static::assertInstanceOf(Video::class, $featuredTheme->video()->first());
});

test('entry', function () {
    $featuredTheme = FeaturedTheme::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $featuredTheme->animethemeentry());
    static::assertInstanceOf(AnimeThemeEntry::class, $featuredTheme->animethemeentry()->first());
});
