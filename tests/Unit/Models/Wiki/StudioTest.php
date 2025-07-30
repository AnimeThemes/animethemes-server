<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
use App\Pivots\Wiki\StudioImage;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('searchable as', function () {
    $studio = Studio::factory()->createOne();

    static::assertIsString($studio->searchableAs());
});

test('to searchable array', function () {
    $studio = Studio::factory()->createOne();

    static::assertIsArray($studio->toSearchableArray());
});

test('nameable', function () {
    $studio = Studio::factory()->createOne();

    static::assertIsString($studio->getName());
});

test('has subtitle', function () {
    $studio = Studio::factory()->createOne();

    static::assertIsString($studio->getSubtitle());
});

test('anime', function () {
    $animeCount = fake()->randomDigitNotNull();

    $studio = Studio::factory()
        ->has(Anime::factory()->count($animeCount))
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $studio->anime());
    static::assertEquals($animeCount, $studio->anime()->count());
    static::assertInstanceOf(Anime::class, $studio->anime()->first());
    static::assertEquals(AnimeStudio::class, $studio->anime()->getPivotClass());
});

test('external resources', function () {
    $resourceCount = fake()->randomDigitNotNull();

    $studio = Studio::factory()
        ->has(ExternalResource::factory()->count($resourceCount), 'resources')
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $studio->resources());
    static::assertEquals($resourceCount, $studio->resources()->count());
    static::assertInstanceOf(ExternalResource::class, $studio->resources()->first());
    static::assertEquals(StudioResource::class, $studio->resources()->getPivotClass());
});

test('images', function () {
    $imageCount = fake()->randomDigitNotNull();

    $studio = Studio::factory()
        ->has(Image::factory()->count($imageCount))
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $studio->images());
    static::assertEquals($imageCount, $studio->images()->count());
    static::assertInstanceOf(Image::class, $studio->images()->first());
    static::assertEquals(StudioImage::class, $studio->images()->getPivotClass());
});
