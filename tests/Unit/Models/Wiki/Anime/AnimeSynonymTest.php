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

    $this->assertInstanceOf(AnimeSynonymType::class, $type);
});

test('searchable as', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    $this->assertIsString($synonym->searchableAs());
});

test('to searchable array', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    $this->assertIsArray($synonym->toSearchableArray());
});

test('nameable', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    $this->assertIsString($synonym->getName());
});

test('has subtitle', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    $this->assertIsString($synonym->getSubtitle());
});

test('anime', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $synonym->anime());
    $this->assertInstanceOf(Anime::class, $synonym->anime()->first());
});
