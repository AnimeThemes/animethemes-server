<?php

declare(strict_types=1);

use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

test('nameable', function (): void {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    expect($featuredTheme->getName())->toBeString();
});

test('has subtitle', function (): void {
    $featuredTheme = FeaturedTheme::factory()
        ->for(Entry::factory()->for(Theme::factory()->for(Anime::factory())))
        ->createOne();

    expect($featuredTheme->getSubtitle())->toBeString();
});

test('casts end at', function (): void {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    expect($featuredTheme->end_at)->toBeInstanceOf(Carbon::class);
});

test('casts start at', function (): void {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    expect($featuredTheme->start_at)->toBeInstanceOf(Carbon::class);
});

test('user', function (): void {
    $featuredTheme = FeaturedTheme::factory()
        ->for(User::factory())
        ->createOne();

    expect($featuredTheme->user())->toBeInstanceOf(BelongsTo::class);
    expect($featuredTheme->user()->first())->toBeInstanceOf(User::class);
});

test('video', function (): void {
    $featuredTheme = FeaturedTheme::factory()
        ->for(Video::factory())
        ->createOne();

    expect($featuredTheme->video())->toBeInstanceOf(BelongsTo::class);
    expect($featuredTheme->video()->first())->toBeInstanceOf(Video::class);
});

test('entry', function (): void {
    $featuredTheme = FeaturedTheme::factory()
        ->for(Entry::factory()->for(Theme::factory()->for(Anime::factory())))
        ->createOne();

    expect($featuredTheme->entry())->toBeInstanceOf(BelongsTo::class);
    expect($featuredTheme->entry()->first())->toBeInstanceOf(Entry::class);
});
