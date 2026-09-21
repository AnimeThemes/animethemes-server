<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Synonym;
use App\Models\Wiki\Theme;
use App\Pivots\Morph\Imageable;
use App\Pivots\Morph\Resourceable;
use App\Pivots\Wiki\AnimeSeries;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('casts season to enum', function (): void {
    $anime = Anime::factory()->createOne();

    $season = $anime->season;

    expect($season)->toBeInstanceOf(AnimeSeason::class);
});

test('casts format to enum', function (): void {
    $anime = Anime::factory()->createOne();

    expect($anime->format)->toBeInstanceOf(AnimeFormat::class);
});

test('searchable as', function (): void {
    $anime = Anime::factory()->createOne();

    expect($anime->searchableAs())->toBeString();
});

test('to searchable array', function (): void {
    $anime = Anime::factory()->createOne();

    expect($anime->toSearchableArray())->toBeArray();
});

test('nameable', function (): void {
    $anime = Anime::factory()->createOne();

    expect($anime->getName())->toBeString();
});

test('has subtitle', function (): void {
    $anime = Anime::factory()->createOne();

    expect($anime->getSubtitle())->toBeString();
});

test('synonyms', function (): void {
    $synonymCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(Synonym::factory()->count($synonymCount))
        ->createOne();

    expect($anime->synonyms())->toBeInstanceOf(MorphMany::class);
    expect($anime->synonyms()->count())->toEqual($synonymCount);
    expect($anime->synonyms()->first())->toBeInstanceOf(Synonym::class);
});

test('series', function (): void {
    $seriesCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(Series::factory()->count($seriesCount))
        ->createOne();

    expect($anime->series())->toBeInstanceOf(BelongsToMany::class);
    expect($anime->series()->count())->toEqual($seriesCount);
    expect($anime->series()->first())->toBeInstanceOf(Series::class);
    expect($anime->series()->getPivotClass())->toEqual(AnimeSeries::class);
});

test('themes', function (): void {
    $themeCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(Theme::factory()->count($themeCount))
        ->createOne();

    expect($anime->themes())->toBeInstanceOf(HasMany::class);
    expect($anime->themes()->count())->toEqual($themeCount);
    expect($anime->themes()->first())->toBeInstanceOf(Theme::class);
});

test('external resources', function (): void {
    $resourceCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(ExternalResource::factory()->count($resourceCount), 'resources')
        ->createOne();

    expect($anime->resources())->toBeInstanceOf(MorphToMany::class);
    expect($anime->resources()->count())->toEqual($resourceCount);
    expect($anime->resources()->first())->toBeInstanceOf(ExternalResource::class);
    expect($anime->resources()->getPivotClass())->toEqual(Resourceable::class);
});

test('images', function (): void {
    $imageCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(Image::factory()->count($imageCount))
        ->createOne();

    expect($anime->images())->toBeInstanceOf(MorphToMany::class);
    expect($anime->images()->count())->toEqual($imageCount);
    expect($anime->images()->first())->toBeInstanceOf(Image::class);
    expect($anime->images()->getPivotClass())->toEqual(Imageable::class);
});

test('studios', function (): void {
    $studioCount = fake()->randomDigitNotNull();

    $anime = Anime::factory()
        ->has(Studio::factory()->count($studioCount))
        ->createOne();

    expect($anime->studios())->toBeInstanceOf(BelongsToMany::class);
    expect($anime->studios()->count())->toEqual($studioCount);
    expect($anime->studios()->first())->toBeInstanceOf(Studio::class);
    expect($anime->studios()->getPivotClass())->toEqual(AnimeStudio::class);
});
