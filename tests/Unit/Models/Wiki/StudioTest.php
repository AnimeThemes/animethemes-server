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

uses(WithFaker::class);

test('searchable as', function () {
    $studio = Studio::factory()->createOne();

    $this->assertIsString($studio->searchableAs());
});

test('to searchable array', function () {
    $studio = Studio::factory()->createOne();

    $this->assertIsArray($studio->toSearchableArray());
});

test('nameable', function () {
    $studio = Studio::factory()->createOne();

    $this->assertIsString($studio->getName());
});

test('has subtitle', function () {
    $studio = Studio::factory()->createOne();

    $this->assertIsString($studio->getSubtitle());
});

test('anime', function () {
    $animeCount = fake()->randomDigitNotNull();

    $studio = Studio::factory()
        ->has(Anime::factory()->count($animeCount))
        ->createOne();

    $this->assertInstanceOf(BelongsToMany::class, $studio->anime());
    $this->assertEquals($animeCount, $studio->anime()->count());
    $this->assertInstanceOf(Anime::class, $studio->anime()->first());
    $this->assertEquals(AnimeStudio::class, $studio->anime()->getPivotClass());
});

test('external resources', function () {
    $resourceCount = fake()->randomDigitNotNull();

    $studio = Studio::factory()
        ->has(ExternalResource::factory()->count($resourceCount), 'resources')
        ->createOne();

    $this->assertInstanceOf(MorphToMany::class, $studio->resources());
    $this->assertEquals($resourceCount, $studio->resources()->count());
    $this->assertInstanceOf(ExternalResource::class, $studio->resources()->first());
    $this->assertEquals(Resourceable::class, $studio->resources()->getPivotClass());
});

test('images', function () {
    $imageCount = fake()->randomDigitNotNull();

    $studio = Studio::factory()
        ->has(Image::factory()->count($imageCount))
        ->createOne();

    $this->assertInstanceOf(MorphToMany::class, $studio->images());
    $this->assertEquals($imageCount, $studio->images()->count());
    $this->assertInstanceOf(Image::class, $studio->images()->first());
    $this->assertEquals(Imageable::class, $studio->images()->getPivotClass());
});
