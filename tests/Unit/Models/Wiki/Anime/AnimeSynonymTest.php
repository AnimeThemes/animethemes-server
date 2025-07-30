<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('casts type to enum', function () {
    $theme = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    $type = $theme->type;

    static::assertInstanceOf(AnimeSynonymType::class, $type);
});

test('searchable as', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    static::assertIsString($synonym->searchableAs());
});

test('to searchable array', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    static::assertIsArray($synonym->toSearchableArray());
});

test('nameable', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    static::assertIsString($synonym->getName());
});

test('has subtitle', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    static::assertIsString($synonym->getSubtitle());
});

test('anime', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $synonym->anime());
    static::assertInstanceOf(Anime::class, $synonym->anime()->first());
});
