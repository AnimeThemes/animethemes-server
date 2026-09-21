<?php

declare(strict_types=1);
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use App\Pivots\Morph\Resourceable;
use App\Pivots\Wiki\EntryVideo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Znck\Eloquent\Relations\BelongsToThrough;

pest()->use(WithFaker::class);

test('searchable as', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    expect($entry->searchableAs())->toBeString();
});

test('to searchable array', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    expect($entry->toSearchableArray())->toBeArray();
});

test('nameable', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    expect($entry->getName())->toBeString();
});

test('has subtitle', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    expect($entry->getSubtitle())->toBeString();
});

test('theme', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    expect($entry->theme())->toBeInstanceOf(BelongsTo::class);
    expect($entry->theme()->first())->toBeInstanceOf(Theme::class);
});

test('external resources', function (): void {
    $resourcesCount = fake()->randomDigitNotNull();

    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->has(ExternalResource::factory()->count($resourcesCount), Entry::RELATION_RESOURCES)
        ->createOne();

    expect($entry->resources())->toBeInstanceOf(MorphToMany::class);
    expect($entry->resources()->count())->toEqual($resourcesCount);
    expect($entry->resources()->first())->toBeInstanceOf(ExternalResource::class);
    expect($entry->resources()->getPivotClass())->toEqual(Resourceable::class);
});

test('videos', function (): void {
    $videoCount = fake()->randomDigitNotNull();

    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->has(Video::factory()->count($videoCount))
        ->createOne();

    expect($entry->videos())->toBeInstanceOf(BelongsToMany::class);
    expect($entry->videos()->count())->toEqual($videoCount);
    expect($entry->videos()->first())->toBeInstanceOf(Video::class);
    expect($entry->videos()->getPivotClass())->toEqual(EntryVideo::class);
});

test('anime', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    expect($entry->anime())->toBeInstanceOf(BelongsToThrough::class);
    expect($entry->anime()->first())->toBeInstanceOf(Anime::class);
});
