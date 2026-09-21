<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Entry;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Pivots\Morph\Resourceable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('casts season to enum', function (): void {
    $resource = ExternalResource::factory()->createOne();

    $site = $resource->site;

    expect($site)->toBeInstanceOf(ResourceSite::class);
});

test('nameable', function (): void {
    $resource = ExternalResource::factory()->createOne();

    expect($resource->getName())->toBeString();
});

test('has subtitle', function (): void {
    $resource = ExternalResource::factory()->createOne();

    expect($resource->getSubtitle())->toBeString();
});

test('anime', function (): void {
    $animeCount = fake()->randomDigitNotNull();

    $resource = ExternalResource::factory()
        ->has(Anime::factory()->count($animeCount))
        ->createOne();

    expect($resource->anime())->toBeInstanceOf(MorphToMany::class);
    expect($resource->anime()->count())->toEqual($animeCount);
    expect($resource->anime()->first())->toBeInstanceOf(Anime::class);
    expect($resource->anime()->getPivotClass())->toEqual(Resourceable::class);
});

test('anime theme entry', function (): void {
    $entryCount = fake()->randomDigitNotNull();

    $resource = ExternalResource::factory()
        ->has(Entry::factory()->forAnime()->count($entryCount))
        ->createOne();

    expect($resource->entries())->toBeInstanceOf(MorphToMany::class);
    expect($resource->entries()->count())->toEqual($entryCount);
    expect($resource->entries()->first())->toBeInstanceOf(Entry::class);
    expect($resource->entries()->getPivotClass())->toEqual(Resourceable::class);
});

test('artists', function (): void {
    $artistCount = fake()->randomDigitNotNull();

    $resource = ExternalResource::factory()
        ->has(Artist::factory()->count($artistCount))
        ->createOne();

    expect($resource->artists())->toBeInstanceOf(MorphToMany::class);
    expect($resource->artists()->count())->toEqual($artistCount);
    expect($resource->artists()->first())->toBeInstanceOf(Artist::class);
    expect($resource->artists()->getPivotClass())->toEqual(Resourceable::class);
});

test('song', function (): void {
    $songCount = fake()->randomDigitNotNull();

    $resource = ExternalResource::factory()
        ->has(Song::factory()->count($songCount))
        ->createOne();

    expect($resource->songs())->toBeInstanceOf(MorphToMany::class);
    expect($resource->songs()->count())->toEqual($songCount);
    expect($resource->songs()->first())->toBeInstanceOf(Song::class);
    expect($resource->songs()->getPivotClass())->toEqual(Resourceable::class);
});

test('studio', function (): void {
    $studioCount = fake()->randomDigitNotNull();

    $resource = ExternalResource::factory()
        ->has(Studio::factory()->count($studioCount))
        ->createOne();

    expect($resource->studios())->toBeInstanceOf(MorphToMany::class);
    expect($resource->studios()->count())->toEqual($studioCount);
    expect($resource->studios()->first())->toBeInstanceOf(Studio::class);
    expect($resource->studios()->getPivotClass())->toEqual(Resourceable::class);
});
