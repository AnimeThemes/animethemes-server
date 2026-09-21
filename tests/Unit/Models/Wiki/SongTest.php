<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Performance;
use App\Models\Wiki\Song;
use App\Models\Wiki\Theme;
use App\Pivots\Morph\Resourceable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('searchable as', function (): void {
    $song = Song::factory()->createOne();

    expect($song->searchableAs())->toBeString();
});

test('to searchable array', function (): void {
    $song = Song::factory()->createOne();

    expect($song->toSearchableArray())->toBeArray();
});

test('nameable', function (): void {
    $song = Song::factory()->createOne();

    expect($song->getName())->toBeString();
});

test('has subtitle', function (): void {
    $song = Song::factory()
        ->has(Theme::factory()->for(Anime::factory()))
        ->createOne();

    expect($song->getSubtitle())->toBeString();
});

test('themes', function (): void {
    $themeCount = fake()->randomDigitNotNull();

    $song = Song::factory()
        ->has(Theme::factory()->for(Anime::factory())->count($themeCount))
        ->createOne();

    expect($song->themes())->toBeInstanceOf(HasMany::class);
    expect($song->themes()->count())->toEqual($themeCount);
    expect($song->themes()->first())->toBeInstanceOf(Theme::class);
});

test('artists', function (): void {
    $artistCount = fake()->randomDigitNotNull();

    $song = Song::factory()
        ->has(Artist::factory()->count($artistCount))
        ->createOne();

    expect($song->artists())->toBeInstanceOf(BelongsToMany::class);
    expect($song->artists()->count())->toEqual($artistCount);
    expect($song->artists()->first())->toBeInstanceOf(Artist::class);
});

test('performances', function (): void {
    $performanceCount = fake()->randomDigitNotNull();

    $song = Song::factory()
        ->has(Performance::factory()->count($performanceCount))
        ->createOne();

    expect($song->performances())->toBeInstanceOf(HasMany::class);
    expect($song->performances()->count())->toEqual($performanceCount);
    expect($song->performances()->first())->toBeInstanceOf(Performance::class);
});

test('external resources', function (): void {
    $resourceCount = fake()->randomDigitNotNull();

    $song = Song::factory()
        ->has(ExternalResource::factory()->count($resourceCount), 'resources')
        ->createOne();

    expect($song->resources())->toBeInstanceOf(MorphToMany::class);
    expect($song->resources()->count())->toEqual($resourceCount);
    expect($song->resources()->first())->toBeInstanceOf(ExternalResource::class);
    expect($song->resources()->getPivotClass())->toEqual(Resourceable::class);
});
