<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Group;
use App\Models\Wiki\Song;
use App\Models\Wiki\Theme;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('casts type to enum', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    $type = $theme->type;

    expect($type)->toBeInstanceOf(ThemeType::class);
});

test('searchable as', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    expect($theme->searchableAs())->toBeString();
});

test('to searchable array', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    expect($theme->toSearchableArray())->toBeArray();
});

test('nameable', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    expect($theme->getName())->toBeString();
});

test('has subtitle', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    expect($theme->getSubtitle())->toBeString();
});

test('anime', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    expect($theme->anime())->toBeInstanceOf(BelongsTo::class);
    expect($theme->anime()->first())->toBeInstanceOf(Anime::class);
});

test('group', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->for(Group::factory())
        ->createOne();

    expect($theme->group())->toBeInstanceOf(BelongsTo::class);
    expect($theme->group()->first())->toBeInstanceOf(Group::class);
});

test('song', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->for(Song::factory())
        ->createOne();

    expect($theme->song())->toBeInstanceOf(BelongsTo::class);
    expect($theme->song()->first())->toBeInstanceOf(Song::class);
});

test('entries', function (): void {
    $entryCount = fake()->randomDigitNotNull();

    $theme = Theme::factory()
        ->for(Anime::factory())
        ->has(Entry::factory()->count($entryCount))
        ->createOne();

    expect($theme->entries())->toBeInstanceOf(HasMany::class);
    expect($theme->entries()->count())->toEqual($entryCount);
    expect($theme->entries()->first())->toBeInstanceOf(Entry::class);
});

test('theme creates slug', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    expect($theme)->toHaveKey('slug');
});
