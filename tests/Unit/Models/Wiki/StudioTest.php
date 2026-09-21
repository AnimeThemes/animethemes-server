<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Morph\Imageable;
use App\Pivots\Morph\Resourceable;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('searchable as', function (): void {
    $studio = Studio::factory()->createOne();

    expect($studio->searchableAs())->toBeString();
});

test('to searchable array', function (): void {
    $studio = Studio::factory()->createOne();

    expect($studio->toSearchableArray())->toBeArray();
});

test('nameable', function (): void {
    $studio = Studio::factory()->createOne();

    expect($studio->getName())->toBeString();
});

test('has subtitle', function (): void {
    $studio = Studio::factory()->createOne();

    expect($studio->getSubtitle())->toBeString();
});

test('anime', function (): void {
    $animeCount = fake()->randomDigitNotNull();

    $studio = Studio::factory()
        ->has(Anime::factory()->count($animeCount))
        ->createOne();

    expect($studio->anime())->toBeInstanceOf(BelongsToMany::class);
    expect($studio->anime()->count())->toEqual($animeCount);
    expect($studio->anime()->first())->toBeInstanceOf(Anime::class);
    expect($studio->anime()->getPivotClass())->toEqual(AnimeStudio::class);
});

test('external resources', function (): void {
    $resourceCount = fake()->randomDigitNotNull();

    $studio = Studio::factory()
        ->has(ExternalResource::factory()->count($resourceCount), 'resources')
        ->createOne();

    expect($studio->resources())->toBeInstanceOf(MorphToMany::class);
    expect($studio->resources()->count())->toEqual($resourceCount);
    expect($studio->resources()->first())->toBeInstanceOf(ExternalResource::class);
    expect($studio->resources()->getPivotClass())->toEqual(Resourceable::class);
});

test('images', function (): void {
    $imageCount = fake()->randomDigitNotNull();

    $studio = Studio::factory()
        ->has(Image::factory()->count($imageCount))
        ->createOne();

    expect($studio->images())->toBeInstanceOf(MorphToMany::class);
    expect($studio->images()->count())->toEqual($imageCount);
    expect($studio->images()->first())->toBeInstanceOf(Image::class);
    expect($studio->images()->getPivotClass())->toEqual(Imageable::class);
});
