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

    $this->assertIsString($featuredTheme->getName());
});

test('has subtitle', function () {
    $featuredTheme = FeaturedTheme::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->createOne();

    $this->assertIsString($featuredTheme->getSubtitle());
});

test('casts end at', function () {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    $this->assertInstanceOf(Carbon::class, $featuredTheme->end_at);
});

test('casts start at', function () {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    $this->assertInstanceOf(Carbon::class, $featuredTheme->start_at);
});

test('user', function () {
    $featuredTheme = FeaturedTheme::factory()
        ->for(User::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $featuredTheme->user());
    $this->assertInstanceOf(User::class, $featuredTheme->user()->first());
});

test('video', function () {
    $featuredTheme = FeaturedTheme::factory()
        ->for(Video::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $featuredTheme->video());
    $this->assertInstanceOf(Video::class, $featuredTheme->video()->first());
});

test('entry', function () {
    $featuredTheme = FeaturedTheme::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $featuredTheme->animethemeentry());
    $this->assertInstanceOf(AnimeThemeEntry::class, $featuredTheme->animethemeentry()->first());
});
